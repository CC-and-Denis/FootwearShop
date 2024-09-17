const image = document.getElementById("magnifying-glass");
const errorMessage = document.getElementById("errorMessage");
const inputText = document.getElementById("researchText") as HTMLInputElement
console.log(1);
document.addEventListener("DOMContentLoaded", () => {
    console.log(2);
    if (image) {
        image.addEventListener("click", () => {
            handleImageClick();
        });
    }
});

function handleImageClick(): void {
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
    }
}