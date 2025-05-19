
class CurrentWeatherBlock
{
    constructor(
        blockId = 'currentWeatherBox',
        cityItemId = 'current-city',
        tempItemId = 'current-temperature',
        descItemId = 'current-description',
        humItemId = 'current-humidity',
    ) {
        this.block = document.getElementById(blockId);
        this.city = document.getElementById(cityItemId);
        this.temperature = document.getElementById(tempItemId);
        this.description = document.getElementById(descItemId);
        this.humidity = document.getElementById(humItemId);
    }

    setup() {

    }

    populate(obj, city) {
        this.city.innerText = city;
        this.temperature.innerText = obj.temperature;
        this.description.innerText = obj.description;
        this.humidity.innerText = obj.humidity;
    }

    clear() {
        this.populate({
            city: "",
            temperature: "",
            description: "",
            humidity: ""
        })
    }

    show(obj, city) {
        this.populate(obj, city);
        this.block.style.display = 'block';
    }

    hide() {
        this.block.style.display ='none';
    }
}
export default CurrentWeatherBlock;
