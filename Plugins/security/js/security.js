import {Hacking} from 'https://cdn.jsdelivr.net/gh/XHiddenProjects/CyberWeb@1.0.1/cyber.min.js';
window.addEventListener('load',()=>{const observer = new MutationObserver((mutations, observer) => {Hacking.xss.sanitize();});observer.observe(document, {subtree: true,attributes: true,});});
