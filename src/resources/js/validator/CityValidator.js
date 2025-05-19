
class CityValidator
{
    static validate(value) {
        return !value || value.length < 2 || value.length > 50
    }
}
export default CityValidator;
