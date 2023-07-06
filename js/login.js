/* JavaScript loader animation */
const load = document.getElementById('loada');
document.addEventListener("DOMContentLoaded", () => {
    load.style.visibility = 'hidden';
})


$(function() {
    
    let er_em = document.getElementById('error_em'),
        er_ps = document.getElementById('error_ps');
    
    // removes all error message
    $("input").on("input change", () => {
        $(".err_msg").text("");
        $("input, select").css("border", "2px solid grey");
    })
    
    //if form is being submitted
    $("form").submit((e) => {
        e.preventDefault(); // it prevents the page from loading
        
        load.style.visibility = 'visible';
        let formData = new FormData(document.querySelector("form"));
        
        //check if it takes too long 
        setTimeout(function() {
            if (load.style.visibility === 'visible') {
                load.style.visibility = 'hidden';
                er_ps.innerText = "Connection Timeout! It seems your connection is too slow.";
            }
        }, 10000)
    
        // send user info to backend
        $.ajax({
            type: "POST",
            url: "../configs/login.php",
            processData: false,
            contentType: false,
            data: formData,
            
            success: function(data) { // if it went successfully
                load.style.visibility = 'hidden';
                let res = JSON.parse(data);
                
                if (res.success !== "") {
                    window.location = "/";
                } 
                else 
                {
                        
                    if (res.error.includes("Email")) { // if email error
                        er_em.innerText = res.error;
                        $("#e").css("border", "1px solid red");
                    } 
                    else { //if password error or other error
                        er_ps.innerText = res.error;
                        $("#p").css("border", "1px solid red");
                    }
                }
            },
            
            error: function() {
                load.style.visibility = 'hidden';
                er_ps.innerText = "There was an Error while trying to Register!";
            }
        })
    })
})