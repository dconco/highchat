$(function() {
    $("#alert").hide();
    $(".menu-close").click(() => {
        $(".menu").slideToggle();
    })
    const main = $("main").html();
    
    /* Navigation bar open */
    $(".nav-list").click(() => {
        $(".nav-full").animate({
            marginRight: "0"
        })
    })
    
    /* Navigation bar close */
    $(".nav-close").click(() => {
        $(".nav-full").animate({
            marginRight: "-90%"
        })
    })
    
    /* SEARCH USER FUNCTION */
    $("#search").on("keyup input change select", () => {
        let q = $("#search").val();
        
        if (q !== "") {
            $.ajax({
                type: "GET",
                url: "/chats/configs/search.php",
                data: "q=" + q,
                success: function(data) {
                    $("main").html(data);
                }
            })
        } else {
            $("main").html(main);
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
        
        setInterval(() => {
            $("*").contextmenu((e) => e.preventDefault())
            $("img").attr("draggable", "false")
        })
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
        
        Alert("Profile Picture Saved to your Device!")
    }
}

// Mark as read function
function read(user_id) {
    $.ajax({
        type: "GET",
        url: "/chats/configs/read.php",
        data: "user_id=" + user_id,
        success: function() {
            window.location.reload();
        }
    })
}

/* MARK AS UNREAD FUNCTION */
function unread(user_id) {
    $.ajax({
        type: "GET",
        url: "/chats/configs/unread.php",
        data: "user_id=" + user_id,
        success: function(data) {
            window.location.reload();
        }
    })
}