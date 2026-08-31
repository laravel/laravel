function g(i,n){return{handler:i,config:n}}g.withOptions=function(i,n=()=>({})){function t(o){return{handler:i(o),config:n(o)}}return t.__isOptionsFunction=!0,t};var u=g;export{u as default};
