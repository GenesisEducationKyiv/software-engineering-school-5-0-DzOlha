import TokenValidator from "../validator/TokenValidator.js";
import MessageBlock from "../blocks/MessageBlock.js";

class TokenForm
{
    constructor(
        messageBlock = new MessageBlock(),
    ) {
        this.messageBlock = messageBlock;
    }

    process = async (apiCallback) => {
        const token = this.validatedToken();
        if(!token) {
            this.messageBlock.show("Invalid token provided", true, 0);
            return;
        }

        try {
            const response = await apiCallback(token);
            this.messageBlock.show(response.message, false, 0);
        }
        catch (err) {
            if (err.validationErrors) {
                const errorMessages = [];
                for (const field in err.validationErrors) {
                    errorMessages.push(...err.validationErrors[field]);
                }
                this.messageBlock.show(
                    `Validation errors: ${errorMessages.join(', ')}`, true, 0
                );
            } else {
                this.messageBlock.show(
                    `Subscription Error: ${err.message || JSON.stringify(err)}`, true, 0
                );
            }
        }
    }

    validatedToken() {
        const params = new URLSearchParams(window.location.search);
        const token = params.get('token');

        if(!TokenValidator.validate(token)) return false;

        return token;
    }
}
export default TokenForm;
