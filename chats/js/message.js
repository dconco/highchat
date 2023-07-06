$(function() {
    $("#send_msg").attr("disabled", "")
    $("#alert").hide()
    $(".short-menu").hide()
    $(".menu-div").hide()
    $(".sticker-wrapper").hide()
    let msg = $("#message");
    var s = false;
    
    document.onload = function() {
        scroll();
    }
    
    $(document).scroll(() => {
        s = true;
    })
    $("main, .main, body").on("mouseout", () => {
        s = false;
    })
    
    /* GET ALL MESSAGES EVERY 500 MS */
    var msgReload = setInterval(function() {
        $.ajax({
            type: "GET",
            url: "/chats/configs/get.php",
            data: "incoming_id=" + incoming_id + "&outgoing_id=" + outgoing_id,
            success: function(data) {
                $(".main").html(data);
                
                /* MARK AS READ FUNCTION */
                let user_id2 = $("#mark").attr("user-id");
                
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/read.php",
                    data: "user_id=" + user_id2
                })
            }
        })
        
        if (s === false) {
            scroll();
        }
    }, 500);
    
    function scroll() {
        window.scrollTo(0, document.body.scrollHeight)
    }
    
    /* MESSAGE FIELD VALIDATE */
    msg.on("input change focus", function(e) {
        $("body").removeClass("active");
        if (msg.val() !== "") {
            $(".msg-menu").fadeOut()
            
            if (msg.val().charAt(0) != " " && msg.val().charAt(0) != "\n" || msg.val().match(/[^\n ]/)) {
                $("#send_msg").removeAttr("disabled")
            } else {
                $("#send_msg").attr("disabled", "")
            }
            
            this.style.height = "auto";
            $(".msg-btn").css("height", "51px")
            this.style.height = this.scrollHeight + "px";
        } else {
            this.style.height = "";
            $(".msg-menu").fadeIn()
            $("#send_msg").attr("disabled", "")
            $(".msg-btn").css("height", "49.9px")
        }
        
        scroll();
    })
    
    /* SEND MESSAGE FUNCTION */
    $("form").submit((e) => {
        e.preventDefault();
        
        let msg = $("#message").val();
        let id1 = $("#message").attr("id1");
        let id2 = $("#message").attr("id2");
        $("#message").focus();
        
        let formData = new FormData();
        formData.append("msg", msg);
        formData.append("outgoing_id", id1);
        formData.append("incoming_id", id2);
        
        $.ajax({
            type: "POST",
            url: "/chats/configs/message.php",
            processData: false,
            contentType: false,
            data: formData,
            success: function(data) {
                if (data == "Message Sent") {
                    if (!$("body").hasClass("active")) {
                        $("#message").val("");
                    }
                    window.scrollTo(0, document.body.scrollHeight+1000);
                } else {
                    Alert(data);
                }
            }
        })
        
        /* MESSAGE FIELD VALIDATE */
        $(".msg-menu").fadeIn();
        $("#send_msg").attr("disabled", "");
        document.getElementById('message').style.height = "50px";
        
        // AUDIO PLAY
        let audio = new Audio();
        audio.src = "../../sounds/chat.mp3";
        audio.play();
        audio.removeNode();
    });
    
    
    /* TOP MENU FUNCTION */
    $(".menu").click(() => {
        $(".menu-div").slideToggle()
    })
    
    /* SHORT MENU FUNCTION */
    $(".msg-menu").click(() => {
        $(".short-menu").slideToggle()
    })
    
    /* Navigation bar close */
    $(".nav-close").click(() => {
        $(".nav-full").animate({
            marginRight: "-90%"
        })
    })
    
    /* CLEAR ALL CHATS FUNCTION */
    $("#clear").click(() => {
        if (confirm("Are you sure to clear all your chats between you and this user?")) {
            $.ajax({
                type: "GET",
                url: "/chats/configs/clearChats.php",
                data: `incoming_id=${incoming_id}`,
                success: function() {
                    Alert("All Messages Cleared Successfully!");
                    scroll();
                    
                    // AUDIO PLAY
                    let audio = new Audio();
                    audio.src = "../../sounds/notification.mp3";
                    audio.play();
                    audio.removeNode();
                }
            })
        }
    })
    
    /* STATUS FUNCTION */
    setInterval(function() {
        $.ajax({
            type: "GET",
            url: "/chats/configs/status.php",
            data: ""
        })
    }, 500);
    
    /* STICKERS FUNCTION */
    $(".close-sticker").click(() => {
        $(".sticker-wrapper").slideUp();
    })
    $("#sticker").click(() => {
        $(".msg-menu").click();
        $(".sticker-wrapper").slideToggle();
    })
    
    /* ARCHIVE CHAT FUNCTION */
    $("#achieve").click(() => {
        let user_id = $("#achieve").attr("user-id");
        let php_achieve = $("#php_achieve").html();
        
        if (php_achieve == "Archive Chat") {
            var achieve = "achieve";
        } else if (php_achieve == "Remove Archive") {
            var achieve = "removeAchieve";
        }
        
        $.ajax({
            type: "GET",
            url: `/chats/configs/${achieve}.php`,
            data: "user_id=" + user_id,
            success: function(data) {
                Alert(data);
            },
            error: function() {
                Alert("Unable to add or remove chat in Achieved list!");
            }
        })
    })
    
    /* BLOCK PROFILE */
    $("#block").click(function() {
        let user_id = $("#block").attr("user-id");
        
        $.ajax({
            type: "GET",
            url: "/chats/configs/block.php",
            data: "user_id=" + user_id,
            success: function(data) {
                if (data === "success") {
                    Alert("User Blocked Successfully!")
                    window.location.reload();
                } 
                else {Alert(data)}
            }
        })
    })
    
    /* UNBLOCK PROFILE */
    $("#unblock").click(function() {
        let user_id = $("#unblock").attr("user-id");
        
        $.ajax({
            type: "GET",
            url: "/chats/configs/unblock.php",
            data: "user_id=" + user_id,
            success: function(data) {
                Alert("You Unblocked this user successfully!")
                window.location.reload();
            }
        })
    })
    
    /* MARK AS READ FUNCTION */
    let user_id2 = $("#mark").attr("user-id");
    
    $.ajax({
        type: "GET",
        url: "/chats/configs/read.php",
        data: "user_id=" + user_id2
    })
    
    /* MARK AS UNREAD FUNCTION */
    $("#mark").click(() => {
        $.ajax({
            type: "GET",
            url: "/chats/configs/unread.php",
            data: "user_id=" + user_id2,
            success: function(data) {
                clearInterval(msgReload);
                Alert("You've successfully marked this chat as Unread!")
                window.location = "/chats/";
            }
        })
    })
    
    /* IMAGE SELECTION */
    $("#image").click(() => {
        const img = document.createElement("input");
        img.setAttribute("type", "file")
        img.value = "";
        img.click()
        
        let int = setInterval(function() {
            if (img.value != "") {
                clearInterval(int)
            }
        })
    });
});

/* JAVASCRIPT LOADER ANIMATION */
document.onreadystatechange = function() {
    if (document.readyState === "complete") {
        $("#preloader").fadeOut("slow");
        
        /* ANIMATION ON SCROLL */
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
        
        setInterval(() => {
            $("*").contextmenu((e) => e.preventDefault())
            $("img").attr("draggable", "false")
        })
    }
}

/* STICKERS FUNCTION 2 */
function Sticker(name) {
    let msg = $("#message"),
        btn = $("#send_msg"),
        close = $(".close-sticker"),
        msgVal = $("#message").val();
    
    msg.val(name);
    if (msg.val() === name) {
        btn.removeAttr("disabled");
        $("body").addClass("active");
        btn.click();
        close.click();
        msg.blur();
        msg.val(msgVal);
        $("body").addClass("active");
    }
}

/* DOWNLOAD PICTURE FUNCTION */
function download(link) {
    if (confirm("You're about to download this user Profile Picture, are you sure to proceed?")) {
        const a = document.createElement("a");
        a.setAttribute("href", "/profile_pictures/" + link);
        a.setAttribute("download", link);
        a.click();
        a.removeNode();
        
        // AUDIO PLAY
        let audio = new Audio();
        audio.src = "../../sounds/notification.mp3";
        audio.play();
        audio.removeNode();
        Alert("Profile Picture Saved to your Device!")
    }
}