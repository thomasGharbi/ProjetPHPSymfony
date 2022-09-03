addInputTalker();

function addInputTalker() {

    let button = document.getElementById('create_conversation_createConversation');
    let input = document.getElementById('create_conversation_talker');
    button.addEventListener('click', function () {

            input.style.display = 'inherit';



    }, false);

    input.addEventListener('click', function () {

        button.type = 'submit';
    })
}