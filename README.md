<!-- ========[  SPYROCHAT VERSION 1.7 ]======== -->
## Spyrochat is a Social Media Messaging Platform that brings the world together to chat. It has many features:

_Group chat_
_End to End Encryption of user informations and messages._
_Followers & Following_
_Message anyone from any country without using number or any other things._
_Send Stickers & any Documents like: video, audio, pictures, files_
_Voice Call and Video Calls_

_Still at development, more features coming up._

<!-- ========[ CREATING DATABASE AND TABLES ]======== -->
CREATE DATABASE highchat:

USERS TABLE
<!-- ========[
CREATE TABLE users (
    id int(11) PRIMARY NOT NULL auto-increase, 
    user_id int(30) NOT NULL, 
    fullname varcher(50) NOT NULL, 
    username varcher(20) NOT NULL, 
    email varcher(225) NOT NULL, 
    gender varcher(10) NOT NULL, 
    admin varcher(10) NOT NULL, 
    status int(20) NOT NULL, 
    img varcher(225) NOT NULL, 
    country varcher(10) NOT NULL, 
    acct_status varcher(10) NOT NULL, 
    otp int(10) NOT NULL, 
    reg_date DATETIME NOT NULL, 
    password varcher(225) NOT NULL
) 14 columns 
] ======== -->

MESSAGES TABLE 
<!-- ========[
CREATE TABLE messages (
    msg_id int(11) NOT NULL auto-increase,
    incoming_msg_id int(30) NOT NULL,
    outgoing_msg_id int(30) NOT NULL,
    msg longtext(10000) NOT NULL,
    status varcher(10) NOT NULL,
    clear_outgoing int(30) NOT NULL,
    clear_incoming int(30) NOT NULL,
    type varcher(10) NOT NULL,
    src varcher(225) NOT NULL,
    encrypt_key varcher(50) NOT NULL,
    iv_value varcher(10) NOT NULL,
    msg_date DATETIME CURRENT_TIMESTAMP NOT NULL
) 12 columns
]======== -->

BLOCK TABLE
<!-- ========[
CREATE TABLE block (
    block int(30) NOT NULL,
    block_by int(30) NOT NULL
)
]======== -->

ACHIEVE TABLE 
<!-- ========[
CREATE TABLE achieve (
    achive_id int(11) NOT NULL auto-increase,
    achive int(30) NOT NULL,
    achive_by int(30) NOT NULL
)
]======== -->

FOLLOW TABLE
<!-- ========[
CREATE TABLE follow (
    id int(11) NOT NULL auto-increase,
    follow int(30) NOT NULL
    follow_by int(30) NOT NULL
)
]======== -->

FRIENDS TABLE
<!-- ========[
CREATE TABLE friends (
    id int(11) NOT NULL auto-increase,
    user_id int(30) NOT NULL,
    friend_id int(30) NOT NULL
)
]======== -->