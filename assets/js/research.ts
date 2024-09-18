const image = document.getElementById("magnifying-glass");
const errorMessage = document.getElementById("errorMessage");
const inputText = document.getElementById("researchText") as HTMLInputElement
const types: HTMLSelectElement = document.getElementById("types") as HTMLSelectElement;
const typesButton = document.getElementById("show_types");
const brandsButton = document.getElementById("show_brands");
const colorsButton = document.getElementById("show_colors");
const typeChoices = document.getElementById("type_choices");
const brandChoices = document.getElementById("brand_choices");
const colorChoices = document.getElementById("color_choices");
var hiddenTypes=true, hiddenBrands=true, hiddenColors = true;

const typeLabels = document.querySelectorAll(".type_label");
const brandLabels = document.querySelectorAll(".brand_label");
const colorLabels = document.querySelectorAll(".color_label");

let selectedTypes: string[] = [];
let selectedBrands: string[] = [];
let selectedColors: string[] = [];

console.log(1);
document.addEventListener("DOMContentLoaded", () => {
    console.log(2);
    if (image) {
        image.addEventListener("click", () => {
            handleImageClick();
        });
    }
});


typesButton.addEventListener("click", () => {
    hiddenTypes = show_choices(hiddenTypes, typeChoices);
});
brandsButton.addEventListener("click", () => {
    hiddenBrands = show_choices(hiddenBrands, brandChoices);
});
colorsButton.addEventListener("click", () => {
    hiddenColors = show_choices(hiddenColors, colorChoices);
});

function show_choices(state, choices) {
    if (state) {
        choices.style.display = "flex";
    } else {
        choices.style.display = "none";
    }
    return !state;
}



// Add a click event listener to each label
typeLabels.forEach((label) => {
    label.addEventListener("click", (event) => {
        selectedTypes = choice(selectedTypes, event);
    });
});

brandLabels.forEach((label) => {
    label.addEventListener("click", (event) => {
        selectedBrands = choice(selectedBrands, event);
    });
});

colorLabels.forEach((label) => {
    label.addEventListener("click", (event) => {
        selectedColors = choice(selectedColors, event);
    });
});

function choice(choicesArray, event) {
    const target = event.target as HTMLElement;
    const genre = target.getAttribute("data-genre");
    let index = choicesArray.indexOf(genre, 0);

    if (genre && !(index > -1)) {
        // Store the clicked genre name in the array
        choicesArray.push(genre);
        target.classList.add("bg-blue");
        console.log("Selected Genres:", choicesArray);
    } else {
        choicesArray.splice(index, 1);
        target.classList.remove("bg-blue");
        console.log("Removed Genre:", genre);
    }

    return choicesArray;
}


function handleImageClick(): void {
    console.log("Final selected types:", selectedTypes);
    console.log("Final selected brands:", selectedBrands);
    console.log("Final selected colors:", selectedColors);
    const isMaleChecked = (document.getElementById("maleCheckbox") as HTMLInputElement).checked;
    const isFemaleChecked = (document.getElementById("femaleCheckbox") as HTMLInputElement).checked;
    const isUnisexChecked = (document.getElementById("unisexCheckbox") as HTMLInputElement).checked;

    const isKidChecked = (document.getElementById("kidCheckbox") as HTMLInputElement).checked;
    const isAdultChecked = (document.getElementById("adultCheckbox") as HTMLInputElement).checked;

    // Get the value of the text input
    // Get the value of the text input
    const textValue = inputText.value.trim();
    console.log(3);
    // Validate if the text input is empty
    if (textValue === "") {
        errorMessage!.style.display = "block";  // Show error message
    } else {
        errorMessage!.style.display = "none";   // Hide error message

        const selected_types = Array.from(types.options, option => option.value);

        // Display values in the console for testing (you can replace this with your own logic)
        let gender: string[] = [];
        if (isMaleChecked) gender.push('male');
        if (isFemaleChecked) gender.push('female');
        if (isUnisexChecked) gender.push('unisex');
        let age: number[] = [];
        if (isKidChecked) age.push(1);
        if (isAdultChecked) age.push(0);


        console.log("Male: ", isMaleChecked);
        console.log("Female: ", isFemaleChecked);
        console.log("Unisex: ", isUnisexChecked);
        console.log("Kid: ", isKidChecked);
        console.log("Adult: ", isAdultChecked);
        console.log("Text Input: ", textValue);
        console.log(selected_types);
    }
}