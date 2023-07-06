function follow(id) {
    let text = document.getElementById(id).innerText;
    
    if (text === "Follow" || text === "Follow Back") 
    {
        $.ajax({
            type: "GET",
            url: "/users/configs/follow.php",
            data: "user_id=" + id,
            success: function(data) 
            {
                if (data === "Friends" || data === "Unfollow") {
                    Alert("You've Followed this user");
                    document.getElementById(id).innerText = data;
                    
                    if (data === "Friends") {
                        document.getElementById(id).style.color = "grey";
                    } else {
                        document.getElementById(id).style.color = "darkred";
                    }
                } else {
                    Alert(data);
                }
            }
        })
    } 
    else 
    { //unfollow or friends
        if (confirm("Do you really want to Unfollow this user?")) 
        {
            $.ajax({
                type: "GET",
                url: "/users/configs/unfollow.php",
                data: "user_id=" + id,
                success: function(data) 
                {
                    if (data === "Follow" || data === "Follow Back") {
                        Alert("You've Unfollowed this user");
                        document.getElementById(id).innerText = data;
                        
                        if (data === "Follow") {
                            document.getElementById(id).style.color = "green";
                        } else {
                            document.getElementById(id).style.color = "blue";
                        }
                    } else {
                        Alert(data);
                    }
                }
            })
        }
    }
}