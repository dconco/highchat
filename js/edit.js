$(function() {
    alert("Note: Leave any field blank if you don't want to update it!! \n\nYou must confirm your current Password to make any changes!");
    $("img").contextmenu((e) => e.preventDefault());
    $("img").attr("draggable", "false");
    $(".alert").hide();
    
    /* STATUS FUNCTION */
    setInterval(function() {
        $.ajax({
            type: "GET",
            url: "/chats/configs/status.php",
            data: ""
        })
    }, 500);
    
    // form updating of user
    $("form").submit((e) => {
        e.preventDefault();
        
        $("input, select, textarea").on("input change", () => {
            $(".alert").hide("slow");
        })
        
        //if password field is empty
        if ($("textarea").val() !== "" && $("textarea").val().length > 200) {
            $(".alert").text("The Bio Field should be less than 200 Characters!")
            $(".alert").removeClass("alert-success")
            $(".alert").addClass("alert-danger")
            $(".alert").show("slow")
            
        } else {
            let formData = new FormData(document.querySelector("form"));
            
            if ($("#username").val() !== "" && $("#username").val().match(/[^.a-zA-Z0-9_-]/))
            {
                $(".alert").text("Username cannot contain Special Character apart from '_', '-', '.'! ")
                $(".alert").removeClass("alert-success")
                $(".alert").addClass("alert-danger")
                $(".alert").show("slow")
                return;
            }
            
            // ajax start function
            $("input, #update, select, textarea").attr("disabled", "");
            $("#update").html('<span class="spinner-border spinner"></span>');
            
            $.ajax({
                type: "POST",
                url: "../configs/edit.php",
                processData: false,
                contentType: false,
                data: formData,
                
                success: function(data) {
                    if (!data.match("!") && data.match("success")) {
                        $(".alert").text("Profile Informations Updated Successfully.")
                        $(".alert").removeClass("alert-danger")
                        $(".alert").addClass("alert-success")
                        $(".alert").show("slow")
                    } else {
                        if (data === "") {
                            $(".alert").text("You haven't make any changes!")
                        } else {
                            $(".alert").text(data.replace("success", " "))
                        }
                            
                        $(".alert").removeClass("alert-success")
                        $(".alert").addClass("alert-danger")
                        $(".alert").show("slow")
                    }
                    
                    $("input, #update, select, textarea").removeAttr("disabled");
                    $("#update").html('Save Changes');
                },
                
                error: function(data) {
                    $(".alert").text("Unable to update Profile Informations!")
                    $(".alert").addClass("alert-danger")
                    $(".alert").removeClass("alert-success")
                    $(".alert").show("slow")
                    
                    $("input, #update, select, textarea").removeAttr("disabled");
                    $("#update").html('Save Changes');
                }
            })
        }
    }) //end form submitting
    
    setInterval(function() {
        $("#val").text($("#image").val());
    }, 500);
});
    
/* JavaScript loader animation */
document.onreadystatechange = function() {
    if (document.readyState === "complete") {
        $("#preloader").fadeOut("slow");
        
        /* Animation on scroll */
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    }
}