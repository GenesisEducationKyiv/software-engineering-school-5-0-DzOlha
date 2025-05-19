
class TokenValidator
{
    static validate(value) {
        const pattern = /^[0-9a-f]{64}$/;
        return typeof value === 'string' && pattern.test(value);
    }
}
export default TokenValidator;
