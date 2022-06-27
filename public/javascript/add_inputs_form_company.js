let array = {
    'add_company_image1': 'add_company_image2',
    'add_company_image2': 'add_company_image3',
    'add_company_image3': 'add_company_image4',
    'add_company_image4': 'add_company_image5',
    'add_company_image5': 'add_company_image1'
};


openInputOtherSector();

function addInputImage() {

    for (let key in array) {
        let input = document.getElementById(key);
        let nextInput = document.getElementById(array[key]);
        let style = window.getComputedStyle(nextInput);
        let display = style.getPropertyValue("display");
        let nextLabel = document.body.querySelector('label[for=' + array[key] + ']');


        input.addEventListener('input', function () {
            if (display === 'none') {
                nextInput.style.display = 'inherit';
                nextLabel.style.display = 'inherit';
            }
        }, false);

    }

}

function openInputOtherSector() {

    let input_other_sector = document.getElementById("user_companies_dashboard_otherSector");
    let input_sector = document.getElementById("user_companies_dashboard_sector");

    if (input_other_sector == null) {
        input_other_sector = document.getElementById("add_company_otherSector");
        input_sector = document.getElementById("add_company_sector");
        addInputImage();

    }

    let label_other_sector = document.body.querySelector('label[id="otherSector"]');

    if (input_sector.value === 'autre') {

        input_other_sector.style.display = 'inherit';
        input_other_sector.required = true;
        label_other_sector.style.display = 'inherit';

    }

    input_sector.addEventListener('input', function () {

        if (input_sector.value === 'autre') {

            input_other_sector.style.display = 'inherit';
            input_other_sector.required = true;
            label_other_sector.style.display = 'inherit';

        } else {
            console.log(input_other_sector,input_sector);
            input_other_sector.style.display = 'none';
            input_other_sector.required = false;
            label_other_sector.style.display = 'none';
            input_other_sector.value = '';
        }
    }, false);


}





