$(function() {
    
    let er_em = document.getElementById('error_em'),
        er_ps = document.getElementById('error_ps'),
        er_ln = document.getElementById('error_ln'),
        er_all = document.getElementById('error_all');
    
    // removes all error message
    $("input, select").on("input change", () => {
        $(".err_msg").text("");
        $("input, select").css("border", "2px solid grey");
    })
    
    //if form is being submitted
    $("form").submit((e) => {
        e.preventDefault(); // it prevents the page from loading
        
        load.style.visibility = 'visible';
        let formData = new FormData(document.querySelector("form"));
        
        // send user info to backend
        $.ajax({
            type: "POST",
            url: "../configs/signup.php",
            processData: false,
            contentType: false,
            data: formData,
            
            success: function(data) { // if it went successfully
                load.style.visibility = 'hidden';
                let res = JSON.parse(data);
                
                if (res.success !== "") {
                    window.location = "/verify/email.php";
                } 
                else 
                {
                        
                    if (res.error.includes("Email")) { // if email error
                        er_em.innerText = res.error;
                        $("#in_em").css("border", "1px solid red");
                    } 
                    else if (res.error.includes("Firstname")) { //if name error
                        er_ln.innerText = res.error;
                        $("#in_ln, #in_fn").css("border", "1px solid red");
                    }
                    else if (res.error.includes("Password")) { //if password error
                        er_ps.innerText = res.error;
                        $("#in_pw1, #in_pw2").css("border", "1px solid red");
                    }
                    else { //if it's other error
                        er_all.innerText = res.error;
                        $("input, select").css("border", "1px solid red");
                    }
                }
            },
            
            error: function() {
                load.style.visibility = 'hidden';
                er_all.text("There was an Error while trying to Register!");
            }
        })
        
        //check if it takes too long 
        setTimeout(function() {
            if (load.style.visibility === 'visible') {
                $(document).ajaxStop();
                
                load.style.visibility = 'hidden';
                er_all.text("Connection Timeout! It seems your connection is too slow.");
            }
        }, 10000);
    })
    
})


/* JavaScript loader animation */
var load = document.getElementById('loada');
document.addEventListener("DOMContentLoaded", () => {
    load.style.visibility = 'hidden';
})