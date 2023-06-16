window.addEventListener("load", function() {
  const r = document.querySelectorAll('a[href*="#brochure"]'), e = document.querySelector("#modal-form"), c = document.querySelector("#modal-form-close");
  let d;
  console.log(e), console.log(getComputedStyle(e).display), e && getComputedStyle(e).display === "block" && (document.body.style.overflow = "hidden"), r && e && c && (r.forEach(function(t) {
    t.addEventListener("click", function(l) {
      l.preventDefault();
      const o = t.getAttribute("href");
      if (o.includes("?")) {
        const s = o.split("?");
        if (s.length > 1) {
          const i = s[1].split("=");
          i[0] === "file" && (d = i[1]);
        }
      }
      e.style.display = "block", document.body.style.overflow = "hidden";
    });
  }), c.addEventListener("click", function() {
    e.style.display = "none", document.body.style.overflow = "auto", document.body.style.overflowX = "hidden";
  }), window.addEventListener("click", function(t) {
    t.target === e && (e.style.display = "none", document.body.style.overflow = "auto", document.body.style.overflowX = "hidden");
  }));
  const n = document.querySelector("#modal-form form");
  n && n.addEventListener("submit", function(t) {
    t.preventDefault();
    const l = new FormData(n);
    d && l.append("file", d);
    const o = new XMLHttpRequest();
    o.open("POST", n.action, !0), o.onreadystatechange = function() {
      o.readyState === 4 && o.status === 200 && (e.querySelector(".message").innerHTML = o.responseText);
    }, o.send(l);
  });
});
