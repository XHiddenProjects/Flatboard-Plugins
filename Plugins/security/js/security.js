import xss from "./xss.min.js"
window.addEventListener('load',()=>{
    const observer = new MutationObserver(() => {
        xss.sanitize();});
    observer.observe(document, {subtree: true,attributes: true,});});
