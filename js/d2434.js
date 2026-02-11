(function(){
function n(v){v=(""+v).replace(",","."); return isFinite(v)?parseFloat(v):null;}
function g(id){return document.getElementById(id);}
function s(id,v){if(g(id))g(id).value=v;}

window.D2434_CalcGeom=function(){
  let D=n(g("D").value), L=n(g("L").value);
  if(!D||!L) return;
  let A=Math.PI*D*D/4, V=A*L;
  s("A",A.toFixed(6));
  s("V",V.toFixed(6));
};

window.D2434_Recalc=function(){
  let A=n(g("A").value), L=n(g("L").value);
  if(!A||!L) return;

  for(let i=1;i<=15;i++){
    let h1=n(g(`h1_${i}`)?.value),
        h2=n(g(`h2_${i}`)?.value),
        Q=n(g(`Q_${i}`)?.value),
        t=n(g(`t_${i}`)?.value);
    if(!h1||!h2||!Q||!t) continue;

    let h=h1-h2;
    let v=Q/(A*t);
    let grad=h/L;
    let K=v/grad;

    s(`h_${i}`,h.toFixed(4));
    s(`v_${i}`,v.toExponential(3));
    s(`i_${i}`,grad.toFixed(4));
    s(`K_${i}`,K.toExponential(3));
    s(`K20_${i}`,K.toExponential(3));
  }
};
})();
