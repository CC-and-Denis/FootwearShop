function login(){
    // TODO
    return;
    //window.location.href = "/Home";
}

function signUp(){
    // TODO
    fetch('/access/signup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email:document.getElementById("email").value,
            username:document.getElementById("username").value,
            password:document.getElementById("password").value
        }),
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById("content").innerHTML=data

        })
      .catch((error) => console.error('Error:', error));
    //window.location.href = "/Home";
}



