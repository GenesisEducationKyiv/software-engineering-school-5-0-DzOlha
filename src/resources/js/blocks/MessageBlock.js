
class MessageBlock
{
    constructor(
        messageId = 'message'
    ) {
        this.messageBlock = document.getElementById(messageId)
    }
    populate(msg) {
        this.messageBlock.innerText = msg;
    }
    clear() {
        this.populate("");
    }
    show(msg, errorMode = true, hideAfter = 5000) {
        this.populate(msg);

        if(errorMode) this.messageBlock.classList.add('error');
        else this.messageBlock.classList.remove('error');

        if(!errorMode) this.messageBlock.classList.add('success');
        else this.messageBlock.classList.remove('success');

        this.messageBlock.style.display = 'block';

        if(hideAfter) {
            setTimeout(() => {
                this.hide();
            }, hideAfter);
        }
    }
    hide() {
        this.clear();
        this.messageBlock.style.display = 'none';
    }
}
export default MessageBlock;
