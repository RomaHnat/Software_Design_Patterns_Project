//All the buttons for calling up form are below

//For Manage Category Section

//Add Category

let addcat = document.getElementById("addcat");
if (addcat) {
    addcat.addEventListener("click", function () {
        fetch('addCategoryForm.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById("displayForm").innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    }, false);
}

//Update Category

let updatecat = document.getElementById("updatecat");
if (updatecat) {
    updatecat.addEventListener("click", function () {
        fetch('updateCategoryForm.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById("displayForm").innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    }, false);
}            

//Add to warehouse

let addtoware = document.getElementById("addtoware");
if (addtoware) {
    addtoware.addEventListener("click", function () {

      fetch('addToWarehouseForm.php')
      .then(response => response.text())
      .then(data => {
          document.getElementById("displayForm").innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
       
                }, false);

}

//List Water

let listwater = document.getElementById("listwater");
if (listwater) {
    listwater.addEventListener("click", function () {
      fetch('listWater.php')
      .then(response => response.text())
      .then(data => {
          document.getElementById("displayForm").innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
}, false);
}

//For Manage Clients section

//Add Client

let addcl = document.getElementById("addcl");
if (addcl) {
    addcl.addEventListener("click", function () {
        fetch('addClientForm.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById("displayForm").innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    }, false);
}

let updatecl = document.getElementById("updatecl");
if (updatecl) {
    updatecl.addEventListener("click", function () {
        fetch('updateClientForm.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById("displayForm").innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    }, false);
}

//List Clients

let listcl = document.getElementById("listcl");
if (listcl) {
    listcl.addEventListener("click", function () {
      fetch('listClients.php')
      .then(response => response.text())
      .then(data => {
          document.getElementById("displayForm").innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
}, false);
}

//For Process Sales section

//Place Sale

let placesale = document.getElementById("placesale");
if (placesale) {
    placesale.addEventListener("click", function () {
      fetch('placeSaleForm.php')
      .then(response => response.text())
      .then(data => {
          document.getElementById("displayForm").innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
}, false);
}

//Cancel Sale

let cancelsale = document.getElementById("cancelsale");
if (cancelsale) {
    cancelsale.addEventListener("click", function () {
      fetch('cancelSaleForm.html')
      .then(response => response.text())
      .then(data => {
          document.getElementById("displayForm").innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
}, false);
}


//All the validations are shown below

//For Manage Category section

function validateCat() {
    
    let name = document.getElementById("name").value;
    let cid = document.getElementById("cid").value;
    let description = document.getElementById("description").value;
    let priceper = document.getElementById("priceper").value;
    let numberof = document.getElementById("numberof").value;
    let letterRegex = /^[A-Za-z]+$/;
    let letterRegexWithSpace = /^[A-Za-z\s]+$/;
    let numericRegex = /^[0-9]+$/;
    let floatRegex = /^[-+]?[0-9]*\.?[0-9]+$/;
  
    if (cid === "" || !letterRegex.test(cid)) {
      window.alert("CategoryID must contain only letters");
      document.getElementById("cid").focus()
      return false;
    }
  
    if (name === "" || !letterRegexWithSpace.test(name)) {
      window.alert("Name must contain only letters");
      document.getElementById("name").focus()
      return false;
    }
  
    if (description === "" || numericRegex.test(description)) {
      window.alert("Description cannot be empty or numeric");
      document.getElementById("description").focus()
      return false;
    }
  
    if (numberof === "" || !numericRegex.test(numberof)) {
      window.alert("Number of products in category must be numeric");
      document.getElementById("numberof").focus()
      return false;
    }
  
    if (priceper === "" || !floatRegex.test(priceper)) {
      window.alert("Price per product in category must be numeric");
      document.getElementById("priceper").focus()
      return false;
    }
    
    return true;
  }
  

  function validateCatUpd() {
    
    let name = document.getElementById("name").value;
    let cid = document.getElementById("cid").value;
    let description = document.getElementById("description").value;
    let priceper = document.getElementById("priceper").value;
    let numberof = document.getElementById("numberof").value;
    let letterRegex = /^[A-Za-z]+$/;
    let letterRegexWithSpace = /^[A-Za-z\s]+$/;
    let numericRegex = /^[0-9]+$/;
    let floatRegex = /^[-+]?[0-9]*\.?[0-9]+$/;
  
    let selectBox = document.getElementById("selectcat");
    let selectedValue = selectBox.value;

    if (selectedValue == "") {

      window.alert("Select the category to update first")
      return false;
    }

    if (cid === "" || !letterRegex.test(cid)) {
      window.alert("CategoryID must contain only letters");
      document.getElementById("cid").focus()
      return false;
    }
  
    if (name === "" || !letterRegexWithSpace.test(name)) {
      window.alert("Name must contain only letters");
      document.getElementById("name").focus()
      return false;
    }
  
    if (description === "" || numericRegex.test(description)) {
      window.alert("Description cannot be empty or numeric");
      document.getElementById("description").focus()
      return false;
    }
  
    if (numberof === "" || !numericRegex.test(numberof)) {
      window.alert("Number of products in category must be numeric");
      document.getElementById("numberof").focus()
      return false;
    }
  
    if (priceper === "" || !floatRegex.test(priceper)) {
      window.alert("Price per product in category must be numeric");
      document.getElementById("priceper").focus()
      return false;
    }
  
    return true;
  }

  function validateAddToWare(){

    let selectBox = document.getElementById("selectcat");
    let selectedValue = selectBox.value;

    let numericRegex = /^[0-9]+$/;

    let toadd = document.getElementById("toadd").value;

    if (selectedValue == "") {

      window.alert("Select the category first")
      return false;
    }

    if (toadd === "" || !numericRegex.test(toadd)) {
      window.alert("Number of products in category must be numeric");
      document.getElementById("toadd").focus()
      return false;
    }

    window.alert( toadd + " of water have been added for category " + selectedValue + ".");
    return true;

  }
  

  //For Manage Clients section

  function validateClient() {
    
    let fname = document.getElementById("fname").value;
    let sname = document.getElementById("sname").value;
    let email = document.getElementById("email").value;
    let letterRegex = /^[A-Za-z]+$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
    if (fname === "" || !letterRegex.test(fname)) {
      window.alert("First Name must contain only letters");
      document.getElementById("fname").focus();
      return false;
    }
  
    if (sname === "" || !letterRegex.test(sname)) {
      window.alert("Second Name must contain only letters");
      document.getElementById("sname").focus()
      return false;
    }
  
    if (email === "" || !emailRegex.test(email)) {
      window.alert("Email must only contain letters, numbers and symbols like \".\" or \"@\"");
      document.getElementById("email").focus()
      return false;
    }

    return true;
  }

  function validateUpdateClient() {
    
    let fname = document.getElementById("fname").value;
    let sname = document.getElementById("sname").value;
    let email = document.getElementById("email").value;
    let clemail = document.getElementById("clemail").value;
    let letterRegex = /^[A-Za-z]+$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
    
    if (clemail === "" || !emailRegex.test(clemail)) {
      window.alert("Email of Client to Update must only contain letters, numbers and symbols like \".\" or \"@\"");
      document.getElementById("clemail").focus();
      return false;
    }

    if (fname === "" || !letterRegex.test(fname)) {
      window.alert("First Name must contain only letters");
      document.getElementById("fname").focus();
      return false;
    }
  
    if (sname === "" || !letterRegex.test(sname)) {
      window.alert("Second Name must contain only letters");
      document.getElementById("sname").focus();
      return false;
    }
  
    if (email === "" || !emailRegex.test(email)) {
      window.alert("Email must only contain letters, numbers and symbols like \".\" or \"@\"");
      document.getElementById("email").focus();
      return false;
    }

    return true;
  }


  //For Process Sales section

  function validateSale(){

    let cart = document.getElementById("cart").value;
    let email = document.getElementById("clientsemail").value;

    if (cart === ""){

        window.alert("The cart is empty");
        return false;

    }

    if (email === ""){


        window.alert("You did not specify an email.")

    }

    return true;

  }