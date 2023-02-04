let form_notice = document.getElementById("add_notice_form")

document.getElementById("add_notices_button").addEventListener('click', function () {
    if(form_notice.classList.contains('d-none')){

        form_notice.classList.remove("d-none")
    }else{

        form_notice.classList.add("d-none")
    }



}, false);
if(document.getElementsByClassName("invalid-feedback").length >= 1) {

    document.getElementById("add_notices_button").click();
}



let general_notice_field = document.getElementById("create_notice_generalNotice");
let quality_notice_field = document.getElementById("create_notice_qualityNotice");
let speed_notice_field = document.getElementById("create_notice_speedNotice");
let price_notice_field = document.getElementById("create_notice_priceNotice");

let generale_notice_value = document.getElementById("general_notice_value");
let quality_notice_value = document.getElementById("quality_notice_value");
let speed_notice_value = document.getElementById("speed_notice_value");
let price_notice_value = document.getElementById("price_notice_value");


generale_notice_value.innerHTML = general_notice_field.value + '/10';

quality_notice_value.innerHTML = quality_notice_field.value + '/10';

speed_notice_value.innerHTML = speed_notice_field.value + '/10';

price_notice_value.innerHTML = price_notice_field.value + '/10';


general_notice_field.addEventListener('change', function () {
    generale_notice_value.innerHTML = general_notice_field.value + '/10'
}, false);


quality_notice_field.addEventListener('click', function () {


    quality_notice_value.innerHTML = quality_notice_field.value + '/10'

}, false);


speed_notice_field.addEventListener('click', function () {


    speed_notice_value.innerHTML = speed_notice_field.value + '/10'

}, false);


price_notice_field.addEventListener('click', function () {


    price_notice_value.innerHTML = price_notice_field.value + '/10'

}, false);