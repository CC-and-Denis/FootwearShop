const preview = document.getElementById("otherImagesPreview") as HTMLDivElement;
const list = document.getElementById("otherImagesList") as HTMLDivElement;
var counter = 0;

if (preview && list) {
    const goBackButton = document.getElementById("goBack");
    const goForwardButton = document.getElementById("goForward");

    if (goBackButton) {
        goBackButton.addEventListener('click', () => {
            scrollOtherImagesInProductPage(-1);
        });
        goBackButton.style.opacity = "0.5";
    }

    if (goForwardButton) {
        goForwardButton.addEventListener('click', () => {
            scrollOtherImagesInProductPage(1);
        });

        // Initialize preview with the first 3 images
        updatePreview();
        updateButtonOpacity();
    }

    function updatePreview() {
        preview.innerHTML = '';
        for (let i = counter; i < counter + 3 && i < list.children.length; i++) {
            let e = list.children[i].cloneNode(true);
            preview.appendChild(e);
        }
    }

    function updateButtonOpacity() {
        if (counter === 0) {
            if (goBackButton) goBackButton.style.opacity = "0.5";
        } else {
            if (goBackButton) goBackButton.style.opacity = "1";
        }

        if (counter >= list.children.length - 3) {
            if (goForwardButton) goForwardButton.style.opacity = "0.5";
        } else {
            if (goForwardButton) goForwardButton.style.opacity = "1";
        }
    }

    function scrollOtherImagesInProductPage(direction: number) {
        if (direction > 0) {
            if (counter >= list.children.length - 3) {
                return;
            }
            counter++;
        } else {
            if (counter <= 0) {
                return;
            }
            counter--;
        }

        updatePreview();
        updateButtonOpacity();
    }
}