
class FrequencyValidator
{
    static validate(value) {
        return ['hourly', 'daily'].includes(value);
    }
}
export default FrequencyValidator;
