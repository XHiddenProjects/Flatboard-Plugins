function RoleTag(){
    let opt = JSON.parse(RoleTagOptions);
    let userGrid = document.querySelectorAll("span.user");
    for(i=0;i<userGrid.length;i++){
        let user = userGrid[i].innerText.match(/^(.+)@?(\S)$/g);
       if(userGrid[i].className.match("admin")){
          userGrid[i].innerHTML = userGrid[i].innerHTML.replace(user, user + "<a style='background-color:"+opt.Admin.btn_color+"; color:"+opt.Admin.txt_color+";font-size:12px;font-weight:bold;' class='badge badge-pill ml-2 admin-badge'>"+opt.Admin.label+"</a>");
       }else if(userGrid[i].className.match("worker")){
              userGrid[i].innerHTML = userGrid[i].innerHTML.replace(user, user + "<a style='background-color:"+opt.Moderator.btn_color+"; color:"+opt.Moderator.txt_color+";font-size:12px;font-weight:bold;' class='badge badge-pill ml-2 worker-badge'>"+opt.Moderator.label+"</a>");
       }else{
               userGrid[i].innerHTML = userGrid[i].innerHTML.replace(user, user + "<a style='background-color:"+opt.Guest.btn_color+"; color:"+opt.Guest.txt_color+";font-size:12px;font-weight:bold;' class='badge badge-pill ml-2 guest-badge'>"+opt.Guest.label+"</a>");
       } 
    }
};
RoleTag();
