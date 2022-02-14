setTimeout(function(){
    let query = document.querySelectorAll(".media-body");
    for(i=0;i<query.length;i++){
let match = query[i].innerHTML.match(/@[A-Za-z0-9_-]*/g);
let r = query[i].innerHTML.replace(/@[A-Za-z0-9_-]*/g, '<span style="color:cyan;font-style:italic;font-weight:bold;">'+match+'</span>');
query[i].innerHTML = r;
    }

}, 0);
