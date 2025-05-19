
class EmailValidator
{
    static validate(value) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(value).toLowerCase());
    }
}
export default EmailValidator;
