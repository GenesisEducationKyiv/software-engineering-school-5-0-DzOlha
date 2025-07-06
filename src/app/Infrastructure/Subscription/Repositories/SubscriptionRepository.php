<?php

namespace App\Infrastructure\Subscription\Repositories;

use App\Domain\Subscription\Entities\Subscription as SubscriptionEntity;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\Email\Email;
use App\Domain\Subscription\ValueObjects\Frequency\Frequency as FrequencyValueObject;
use App\Domain\Subscription\ValueObjects\Status\Status;
use App\Domain\Subscription\ValueObjects\Token\Token as TokenValueObject;
use App\Domain\Subscription\ValueObjects\Token\TokenType;
use App\Domain\Weather\ValueObjects\City\City as CityValueObject;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Infrastructure\Subscription\Models\City;
use App\Infrastructure\Subscription\Models\Frequency;
use App\Infrastructure\Subscription\Models\Subscription;
use App\Infrastructure\Subscription\Models\SubscriptionEmail;
use App\Infrastructure\Subscription\Models\SubscriptionToken;
use App\Infrastructure\Subscription\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * @param SubscriptionEntity $entity
     * @return SubscriptionEntity
     * @throws FrequencyNotFoundException
     */
    public function save(SubscriptionEntity $entity): SubscriptionEntity
    {
        return DB::transaction(function () use ($entity) {
            $usersTable = User::getTableName();
            $citiesTable = City::getTableName();
            $frequenciesTable = Frequency::getTableName();
            $subscriptionsTable = Subscription::getTableName();

            $userEmail = $entity->getEmail()->getValue();

            $existingUserId = DB::table($usersTable)
                ->where('email', $userEmail)
                ->value('id');

            if (!$existingUserId) {
                $userId = DB::table($usersTable)->insertGetId([
                    'email'      => $userEmail,
                    'created_at' => now()
                ]);
            } else {
                $userId = $existingUserId;
            }

            $cityName = $entity->getCity()->getName();

            $existingCityId = DB::table($citiesTable)
                ->where('name', $cityName)
                ->value('id');

            if (!$existingCityId) {
                $cityId = DB::table($citiesTable)->insertGetId([
                    'name'       => $cityName,
                    'created_at' => now()
                ]);
            } else {
                $cityId = $existingCityId;
            }

            $frequencyName = $entity->getFrequency()->getName();

            $frequencyId = DB::table($frequenciesTable)
                ->where('name', $frequencyName)
                ->value('id');

            if (!$frequencyId) {
                throw new FrequencyNotFoundException();
            }

            $subscriptionId = DB::table($subscriptionsTable)->insertGetId([
                'user_id'      => $userId,
                'city_id'      => $cityId,
                'frequency_id' => $frequencyId,
                'status'       => $entity->getStatus()->getValue(),
                'created_at'   => now()
            ]);

            $entity->setId($subscriptionId);

            if ($entity->getConfirmationToken() !== null) {
                $this->saveToken(
                    $subscriptionId,
                    $entity->getConfirmationToken()->getValue(),
                    'confirm',
                    $entity->getConfirmationToken()->getExpiresAt()
                );
            }

            if ($entity->getUnsubscribeToken() !== null) {
                $this->saveToken(
                    $subscriptionId,
                    $entity->getUnsubscribeToken()->getValue(),
                    'cancel'
                );
            }

            return $entity;
        });
    }

    public function findSubscriptionById(int $id): ?SubscriptionEntity
    {
        $subscriptionTable = Subscription::getTableName();
        $userTable = User::getTableName();
        $cityTable = City::getTableName();
        $frequencyTable = Frequency::getTableName();
        $tokenTable = SubscriptionToken::getTableName();

        /** @var object{
         *     id: int,
         *     status: string,
         *     user_email: string,
         *     city_name: string,
         *     frequency_id: int,
         *     frequency_name: string
         *   }
         *   |null $subscription
         */
        $subscription = DB::table("{$subscriptionTable} as s")
            ->join("{$userTable} as u", 's.user_id', '=', 'u.id')
            ->join("{$cityTable} as c", 's.city_id', '=', 'c.id')
            ->join("{$frequencyTable} as f", 's.frequency_id', '=', 'f.id')
            ->where('s.id', $id)
            ->select(
                's.id',
                's.status',
                'u.email as user_email',
                'c.name as city_name',
                'f.id as frequency_id',
                'f.name as frequency_name'
            )
            ->first();

        if (!$subscription) {
            return null;
        }

        /** @var Collection<string, object{
         *     token: string,
         *     type: string,
         *     expires_at: string|null
         * }> $tokens
         */
        $tokens = DB::table($tokenTable)
            ->where('subscription_id', $subscription->id)
            ->whereIn('type', ['confirm', 'cancel'])
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get()
            ->keyBy('type');

        $subscriptionEntity = new SubscriptionEntity(
            new Email($subscription->user_email),
            new CityValueObject($subscription->city_name),
            FrequencyValueObject::fromId($subscription->frequency_id, $subscription->frequency_name),
            Status::fromString($subscription->status)
        );
        $subscriptionEntity->setId($subscription->id);

        if (isset($tokens['confirm'])) {
            $token = $tokens['confirm'];
            $expiresAt = $token->expires_at ? new \DateTimeImmutable($token->expires_at) : null;
            $subscriptionEntity->setConfirmationToken(new TokenValueObject(
                $token->token,
                TokenType::fromString($token->type),
                $expiresAt
            ));
        }

        if (isset($tokens['cancel'])) {
            $token = $tokens['cancel'];
            $subscriptionEntity->setUnsubscribeToken(new TokenValueObject(
                $token->token,
                TokenType::fromString($token->type),
                null
            ));
        }

        return $subscriptionEntity;
    }

    private function saveToken(
        int $subscriptionId,
        string $token,
        string $type,
        ?\DateTimeInterface $expiresAt = null
    ): void {
        $table = SubscriptionToken::getTableName();

        DB::table($table)->insert([
            'subscription_id' => $subscriptionId,
            'token'           => $token,
            'type'            => $type,
            'expires_at'      => $expiresAt,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    public function hasActiveSubscription(SubscriptionEntity $entity): bool
    {
        $subs = Subscription::getTableName();
        $users = User::getTableName();
        $cities = City::getTableName();
        $freqs = Frequency::getTableName();

        return DB::table($subs)
            ->join($users, "$subs.user_id", '=', "$users.id")
            ->join($cities, "$subs.city_id", '=', "$cities.id")
            ->join($freqs, "$subs.frequency_id", '=', "$freqs.id")
            ->where([
                ["$users.email", '=', $entity->getEmail()->getValue()],
                ["$cities.name", '=', $entity->getCity()->getName()],
                ["$freqs.name", '=', $entity->getFrequency()->getName()],
                ["$subs.status", '=', 'active'],
            ])
            ->exists();
    }

    /**
     * @param SubscriptionEntity $entity
     * @return int|null
     */
    public function getPendingSubscriptionId(SubscriptionEntity $entity): ?int
    {
        $subs = Subscription::getTableName();
        $users = User::getTableName();
        $cities = City::getTableName();
        $freqs = Frequency::getTableName();

        $pendingSubscriptionId = DB::table($subs)
            ->join($users, "$subs.user_id", '=', "$users.id")
            ->join($cities, "$subs.city_id", '=', "$cities.id")
            ->join($freqs, "$subs.frequency_id", '=', "$freqs.id")
            ->where([
                ["$users.email", '=', $entity->getEmail()->getValue()],
                ["$cities.name", '=', $entity->getCity()->getName()],
                ["$freqs.name", '=', $entity->getFrequency()->getName()],
                ["$subs.status", '=', 'pending'],
            ])
            ->value("$subs.id");

        if (!is_numeric($pendingSubscriptionId)) {
            return null;
        }

        return (int)$pendingSubscriptionId;
    }

    public function hasValidConfirmationToken(int $subscriptionId): bool
    {
        $table = SubscriptionToken::getTableName();

        return DB::table($table)
            ->where('subscription_id', $subscriptionId)
            ->where('type', 'confirm')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function replaceTokensForPending(
        int $subscriptionId,
        TokenValueObject $confirm,
        TokenValueObject $cancel
    ): void {
        $table = SubscriptionToken::getTableName();

        DB::table($table)->where('subscription_id', $subscriptionId)->delete();

        DB::table($table)->insert([
            [
                'subscription_id' => $subscriptionId,
                'token'           => $confirm->getValue(),
                'type'            => 'confirm',
                'expires_at'      => $confirm->getExpiresAt(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'subscription_id' => $subscriptionId,
                'token'           => $cancel->getValue(),
                'type'            => 'cancel',
                'expires_at'      => $cancel->getExpiresAt(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }

    public function delete(int $subscriptionId): bool
    {
        $table = Subscription::getTableName();

        return DB::table($table)
                ->where('id', $subscriptionId)
                ->delete() > 0;
    }

    public function confirmSubscriptionByToken(string $token): ?SubscriptionEntity
    {
        $subscriptionTokensTable = SubscriptionToken::getTableName();
        $subscriptionsTable = Subscription::getTableName();

        return DB::transaction(function () use ($token, $subscriptionTokensTable, $subscriptionsTable) {
            /** @var object{id: int, subscription_id: int}|null $tokenRecord */
            $tokenRecord = DB::table($subscriptionTokensTable)
                ->where('token', $token)
                ->where('type', 'confirm')
                ->where('expires_at', '>', now())
                ->select('id', 'subscription_id')
                ->first();

            if (!$tokenRecord) {
                return null;
            }

            $updated = DB::table($subscriptionsTable)
                ->where('id', $tokenRecord->subscription_id)
                ->update(['status' => 'active']);

            if (!$updated) {
                return null;
            }

            DB::table($subscriptionTokensTable)
                ->where('id', $tokenRecord->id)
                ->delete();

            return $this->findSubscriptionById($tokenRecord->subscription_id);
        });
    }

    public function unsubscribeByToken(string $token): ?SubscriptionEntity
    {
        $subscriptionTokensTable = SubscriptionToken::getTableName();
        $subscriptionsTable = Subscription::getTableName();

        return DB::transaction(function () use ($token, $subscriptionTokensTable, $subscriptionsTable) {
            /** @var object{subscription_id: int}|null $tokenRecord */
            $tokenRecord = DB::table($subscriptionTokensTable)
                ->where('token', $token)
                ->where('type', 'cancel')
                ->select('subscription_id')
                ->first();

            if (!$tokenRecord) {
                return null;
            }

            $subscriptionEntity = $this->findSubscriptionById($tokenRecord->subscription_id);

            if (!$subscriptionEntity) {
                return null;
            }

            DB::table($subscriptionsTable)
                ->where('id', $tokenRecord->subscription_id)
                ->delete();

            return $subscriptionEntity;
        });
    }

    public function updateSubscriptionEmailStatus(
        int $subscriptionId,
        int $intervalMinutes,
        bool $success = true
    ): bool {
        $status = $success ? 'success' : 'error';

        $now = now();
        $nextScheduled = $now->copy()->addMinutes($intervalMinutes);

        $table = SubscriptionEmail::getTableName();

        /** @var int|null $existingId */
        $existingId = DB::table($table)
            ->where('subscription_id', $subscriptionId)
            ->value('id');

        if ($existingId !== null) {
            return DB::table($table)
                    ->where('subscription_id', $subscriptionId)
                    ->update([
                        'last_sent_at'      => $now,
                        'next_scheduled_at' => $nextScheduled,
                        'status'            => $status,
                        'updated_at'        => $now,
                    ]) > 0;
        }

        return DB::table($table)->insert([
            'subscription_id'   => $subscriptionId,
            'last_sent_at'      => $now,
            'next_scheduled_at' => $nextScheduled,
            'status'            => $status,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);
    }

    public function expireConfirmationToken(int $subscriptionId): bool
    {
        $tokensTable = SubscriptionToken::getTableName();

        return DB::table($tokensTable)
                ->where('subscription_id', $subscriptionId)
                ->where('type', 'confirm')
                ->update([
                    'expires_at' => now()->subHour(),
                    'updated_at' => now(),
                ]) > 0;
    }
}
