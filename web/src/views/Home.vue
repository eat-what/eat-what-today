<template>
  <div class="home">
    <img src="../assets/home-black-bg.jpg" class="star-bg">
    <div class="canvas">
        <div id="drawing"></div>
        <div ref="star"></div>
        <img src="../assets/earth-ignore.png" class="circle earth">
        <img src="../assets/c3-ignore.png" class="circle c2">
        <img src="../assets/c3-ignore.png" class="circle c3">
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import HelloWorld from '@/components/HelloWorld.vue' // @ is an alias to /src
import SVG from 'svg.js'
(window as any).THREE = require('three/build/three.js');
require('three/examples/js/renderers/Projector.js')
require('three/examples/js/renderers/CanvasRenderer.js')
const THREE = (window as any).THREE;

@Component({
  components: {
  HelloWorld,
  },
  })
export default class Home extends Vue {
  public container: any;
  public camera: any;
  public scene: any;
  public renderer: any;
  public group: any;
  public particle: any;
  public mouseX: any = 0;
  public mouseY: any = 0;

  public windowHalfX: any = window.innerWidth / 2;
  public windowHalfY: any = window.innerHeight / 2;

  public texts: any = [
    "eat what",
    "at what",
    "t what",
    "what",
    "hat",
    "at",
    "t",
    "eat what",
    "at what",
    "t what",
    "what",
    "hat",
    "at",
    "t",
    "eat what",
    "at what",
    "t what",
    "what",
    "hat",
    "at",
    "t"
  ]
  public point: any = [];

  public draw: any = null;
  public last: any = '';

  public mounted (): void {
    this.draw = SVG('drawing').size("100%", "100%");
    this.initSvg();
    this.init();
    this.animate();
  }
  public initSvg () : void {
    this.draw.clear();
    this.initPoint();
    this.point.forEach( (item: any) => {
      this.mark(item);
    })
  }
  public init (): void {

    // this.container = document.createElement( 'div' );
    // document.body.appendChild( this.container );
    this.container = this.$refs.star;

    this.camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 1e4 );
    this.camera.position.z = 1500;

    this.scene = new THREE.Scene();

    let PI2 = Math.PI * 2;
    let program = function ( context: any ) {

      context.beginPath();
      context.arc( 0, 0, 0.1, 0, PI2, true );
      context.fill();

    };

    this.group = new THREE.Group();
    this.scene.add( this.group );

    for ( let i = 0; i < 300; i++ ) {

      let material = new THREE.SpriteCanvasMaterial( {
        color: 0xf4f4f4,
        // color: Math.random() * 0x808008 + 0x808080,
        program: program
      } );
      let n = this.T(-3.5, 3.5);
      let r = this.N(n, 0, 2);

      this.particle = new THREE.Sprite( material );
      this.particle.position.x = n * 500;
      this.particle.position.y = this.T(-r, r) * 1400;
      this.particle.position.z = Math.random() * 800 - 400;
      this.particle.scale.x = this.particle.scale.y = Math.random() * 20 + 10;
      this.group.add( this.particle );
    }

    this.renderer = new THREE.CanvasRenderer({
      alpha: true
    });
    this.renderer.setPixelRatio( window.devicePixelRatio );
    this.renderer.setSize( window.innerWidth, window.innerHeight - 56 );
    this.container.appendChild( this.renderer.domElement );

    document.addEventListener( 'mousemove', this.onDocumentMouseMove, false );
    document.addEventListener( 'touchstart', this.onDocumentTouchStart, false );
    document.addEventListener( 'touchmove', this.onDocumentTouchMove, false );

    //

    window.addEventListener( 'resize',this.onWindowResize, false );
  }
  public T(e: any, t: any): any {
      return Math.random() * (t - e) + e
  }

  public N(e: any, t: any, n: any): any {
      t = t || 0;
      n = n || 1;
      return 1 / (Math.sqrt(n) * Math.sqrt(2 * Math.PI)) * Math.pow(Math.E, -(e * e + 0 - t) / (2 * n))
  }

  public onWindowResize(): void {

    this.windowHalfX = window.innerWidth / 2;
    this.windowHalfY = window.innerHeight / 2;

    this.camera.aspect = window.innerWidth / window.innerHeight;
    this.camera.updateProjectionMatrix();

    this.renderer.setSize( window.innerWidth, window.innerHeight );

    this.debounce(1000, this.initSvg);
  }

  public debounce(idle: any, action: any): any {
    this.last && clearTimeout(this.last);
    this.last = setTimeout(action, idle)
  }
  //

  public onDocumentMouseMove( event: any ): void {
    this.mouseX = event.clientX - this.windowHalfX;
    this.mouseY = event.clientY - this.windowHalfY;
  }

  public onDocumentTouchStart( event: any ): void {

    if ( event.touches.length === 1 ) {

      event.preventDefault();

      this.mouseX = event.touches[ 0 ].pageX - this.windowHalfX;
      this.mouseY = event.touches[ 0 ].pageY - this.windowHalfY;

    }

  }

  public onDocumentTouchMove( event: any ): void {

    if ( event.touches.length === 1 ) {

      event.preventDefault();

      this.mouseX = event.touches[ 0 ].pageX - this.windowHalfX;
      this.mouseY = event.touches[ 0 ].pageY - this.windowHalfY;

    }

  }

  public renders(): void {
    // this.camera.position.x += ( this.mouseX - this.camera.position.x ) * 0.05;
    // this.camera.position.y += ( - this.mouseY - this.camera.position.y ) * 0.05;
    this.camera.position.x += ( this.mouseX - this.camera.position.x ) * 0.05;

    this.camera.lookAt( this.scene.position );

    // this.group.rotation.x += 0.01;
    // this.group.rotation.y += 0.02;
    this.renderer.render( this.scene, this.camera );

  }

  public animate(): void {
    // this.renderer.render( this.scene, this.camera );

    requestAnimationFrame( this.animate );

    this.renders();
  }

  public initPoint(): void {
    this.point = [];
    let windowHalfX: any = window.innerWidth / 2;
    let windowHalfY: any = window.innerHeight / 2;

    let n: any = this.T(-3.5, 3.5);

    let r: any = windowHalfX / 2;
    let a: any = windowHalfX;
    let b: any = windowHalfY;

    const color: any = {
      0: '#fff',
      1: '#ffde00',
      2: '#ff6e6e',
      3: '#48f0ff'
    }

    this.texts.forEach( (item: any) => {

      let cx = this.T(-r, r) + windowHalfX;
      let sign = this.T(-1, 1) > 0 ? 1:-1
      let cy = sign * Math.sqrt( r*r - Math.pow(cx - windowHalfX ,2));

      this.point.push({
        position: {
          cx: cx,
          cy: cy + windowHalfY + Math.random() * 200 - 200
        },
        fill: color[Math.floor(Math.random() * 3) ],
        size: 12,
        text: {
            text: item,
            dx: 12,
            dy: 12,
            font: {
              fill: "#fff",
              family: "",
              size: 14,
            }
        }
      })
    })
  }

  public mark (mark: any) : void {

    let text: any  = this.draw.text(mark.text.text).font(mark.text.font).center(mark.position.cx + mark.text.dx,mark.position.cy + mark.text.dy);

    let circle1: any  = this.draw.circle(mark.size).center(mark.position.cx, mark.position.cy).attr({ fill: '#fff' ,opacity: 0.7})
    circle1.animate(2000, '>').radius(mark.size * 2).fill({ color: '#fff', opacity: 0 }).loop()
    circle1.hide();

    let circle2: any  = this.draw.circle(mark.size * 2 ).center(mark.position.cx, mark.position.cy).attr({ fill: '#fff' ,opacity: 0.7})
    circle2.animate(2000, '>').radius(mark.size * 4).fill({ color: '#fff', opacity: 0 }).loop()
    circle2.hide();

    let circle: any = this.draw.circle(mark.size).center(mark.position.cx, mark.position.cy).attr({ fill: mark.fill })

    circle.mouseover(function() { 
      if(circle1.visible()) {
        return;
      }
      circle1.show();
      circle2.show();
    })
    circle.mouseout(function() {
        circle1.hide();
        circle2.hide();
    })
  }
}
</script>

<style lang="scss">
    .home {
        position: relative;
        height: auto;
        min-height: 100%;
        background-color: #f9f9f9;
        height: calc(100vh - 56px);
    }
    .canvas {
        position: absolute;
        z-index: 1001;
        top: 0;
        overflow: hidden;
        height: 100%;
        width: 100%;
    }
    .star-bg {
        display: block;
        height: 100%;
        position: absolute;
        width: 100%;
        left: 0px;
        right: 0px;
    }
    .circle {
        position: absolute;
        top: 50%;
        left: 50%;
    }
    .earth {
        width: 416px;
        height: 416px;
        transform: translate( -50%, -50%);
    }
    .c2 {
        width: 416px;
        height: 416px;
        margin-left: -208px;
        margin-top: -208px;
        animation: leftCircle 180s linear infinite;
    }
    .c3 {
        width: 773px;
        height: 773px;
        margin-left: -366px;
        margin-top: -366px;
        animation: rightCircle 180s linear infinite;
    }

    @keyframes leftCircle
    {
     0%   {
        transform: rotate(360deg);}
     100% {
        transform: rotate(0deg);}
    }

    @keyframes rightCircle
    {
     0%   {
        transform: rotate(0deg);}
     100% {
        transform: rotate(360deg);}
    }
    #drawing {
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: 10004;
    }
    @media only screen and (max-width: 479px) { 
      .earth {
          width: 200px;
          height: 200px;
      }
      .c2 {
          width: 200px;
          height: 200px;
          margin-left: -100px;
          margin-top: -100px;
      }
      .c3 {
          width: 400px;
          height: 400px;
          margin-left: -200px;
          margin-top: -200px;
      }
    }


    @media screen and (min-width: 480px) and (max-width: 959px) {
      .earth {
          width: 300px;
          height: 300px;
      }
      .c2 {
          width: 300px;
          height: 300px;
          margin-left: -150px;
          margin-top: -150px;
      }
      .c3 {
          width: 600px;
          height: 600px;
          margin-left: -300px;
          margin-top: -300px;
      }
    }

    @media screen and (min-width: 960px) and (max-width: 1199px) {
      .earth {
          width: 300px;
          height: 300px;
      }
      .c2 {
          width: 300px;
          height: 300px;
          margin-left: -150px;
          margin-top: -150px;
      }
      .c3 {
          width: 700px;
          height: 700px;
          margin-left: -350px;
          margin-top: -350px;
      }
    }
</style>
