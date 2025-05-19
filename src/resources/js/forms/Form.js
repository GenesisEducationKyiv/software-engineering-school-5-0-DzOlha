
class Form
{
    showLoader(btn) {
        btn.innerHTML = '<div class="spinner"></div>';
        btn.disabled = true;
    }
    hideLoader(btn, text) {
        btn.innerHTML = text;
        btn.disabled = false;
    }
}
export default Form;
