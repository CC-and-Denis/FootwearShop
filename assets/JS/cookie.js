// questo andrà legato alla homePage


window.addEventListener('load', function() {

    //const tipo = document.getElementById("tipo");
    //const marca = document.getElementById("marca");
    //const colore = document.getElementById("colore");

    //updateCookie("Tipo", tipo.value)
    //updateCookie("Marca", marca.value)
    //updateCookie("Colore", colore.value)

    console.log("hey")

    fetch('/api/fyp-function')
    .then(response => response.json())              // process the JSON response form the PHP function
    .then(data => {
        // Display the result
        if (data.result) {

            document.querySelector('.fyp_image1').style.backgroundImage = "url('" + data[0].img + "')";
            //setInnerText('fyp_image1', data[0].img)
            //setInnerText('fyp_image2', data[1].img)
            //setInnerText('fyp_image3', data[2].img)
            //setInnerText('fyp_image4', data[3].img)

            document.querySelector('.description').textContent = data[0].description;

            //setInnerText('fyp_descr1', data[0].description)
            //setInnerText('fyp_descr2', data[1].description)
            //setInnerText('fyp_descr3', data[2].description)
            //setInnerText('fyp_descr4', data[3].description)

            document.querySelector('.shoe_name').textContent = data[0].brand + " " + data[0].model;

            //setInnerText('fyp_brand1', data[0].brand)
            //setInnerText('fyp_brand2', data[1].brand)
            //setInnerText('fyp_brand3', data[2].brand)
            //setInnerText('fyp_brand4', data[3].brand)

            //...
            //...
        } else {
            document.getElementById('result').innerText = 'Error: ' + data.error;
        }
    })
})

function setInnerText(id, text) {
    document.getElementById(id).innerText = text;
}




// ( il data sarà una cosa del genere
/*
    $data = [
        ['id' => '...', 'img' => 'percorso', 'nome' => '...', 'descrizione' => '...'],
        ['id' => '...', 'img' => 'percorso', 'nome' => '...', 'descrizione' => '...'],
        ['id' => '...', 'img' => 'percorso', 'nome' => '...', 'descrizione' => '...'],
    ];
*/
// )


function updateCookie(title, name) {
    values = getCookie(title)
    values.name += 1
    values = JSON.stringify(values)
    document.cookie = title + "=" + values + "expires = Mer, 1 January 2200 00:00:00 UTC; path=/"
}

function getCookie(title) {
    const decodCookie = decodeURIComponent(document.cookie);
    const arrayCookie = decodCookie.split("; ");

    arrayCookie.forEach(element => {
        if (element.indexOf(title) == 0) {
            result = element.substring(title.lenght() + 1)
            return JSON.parse(result)
        }
    })
}