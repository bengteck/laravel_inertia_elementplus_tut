// ==UserScript==
// @name         New Userscript
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       You
// @match        https://www.vschooltrend.com/
// @match        https://www.vschooltrend.com/web
// @icon         https://www.google.com/s2/favicons?domain=vschooltrend.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

const u = document.querySelector("[name='username']")
const p = document.querySelector("[name='password']")
const s = document.querySelector("[type='submit']")
const here = document.querySelector("[class='logo']");

if(! (u && p && s && here) ) {
    console.error('fail to find login section')
    return;
}

const accounts = [
{ n: 'vschoolsk3', p: 'vschool', d:'sk3' },
{ n: 'vschoolsk6', p: 'vschool', d:'sk6' },
{ n: 'vschoolsmk3', p: 'vschool', d:'smk3'},
{ n: 'vschoolsmk5', p: 'vschool', d:'smk5'},
];

const style = document.createElement('style')
document.getElementsByTagName("head")[0].appendChild(style);
style.textContent = `
.loginlinklist li {
    list-style-type: none;
    display: inline-block;
    padding: 0px 10px;
}
.demo-login {
    position:absolute;
    top: 6px;
    right: 158px;
    color:black;
    background-color:white;
}
`

const span = document.createElement('span')
here.parentElement.appendChild(span)
span.innerHTML=`<div class="demo-login"><ul class='loginlinklist'></ul></div>`

const ul = span.getElementsByClassName('loginlinklist')[0]
accounts.forEach((x) => {
    const a = document.createElement('a')
    a.style.color="black"
    a.innerHTML = x.d
    a.href = '#'
    a.onclick = () => {
        u.value=x.n
        p.value=x.p
        s.click()
        return false
    }

    const li = document.createElement('li')
    li.appendChild(a)

    ul.appendChild(li)

})

})();