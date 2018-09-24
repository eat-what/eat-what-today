<template>
  <div class="circle-press">
    <img src="../assets/home-black-bg.jpg" class="star-bg">
    <div id="drawing"></div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import HelloWorld from '@/components/HelloWorld.vue' // @ is an alias to /src
import SVG from 'svg.js'
@Component({
  components: {
  HelloWorld,
  },
  })
export default class CirclePress extends Vue {

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

  public windowHalfX: any = window.innerWidth / 2;
  public windowHalfY: any = window.innerHeight / 2 - 28;
  public radius:any = 120;

  public mounted (): void {
    this.draw = SVG('drawing').size("100%", "100%");
    if(window.innerWidth < 780) {
      this.radius = 75;
    }
    this.initSvg();
    window.addEventListener( 'resize', this.onWindowResize, false );
  }


  public initSvg () : void {
    this.draw.clear();
    this.initBg();
    this.circleButton(this.radius);
    this.initCircle(this.radius);
    this.initStar(200);
  }

  public onWindowResize(): void {
    this.windowHalfX = window.innerWidth / 2;
    this.windowHalfY = window.innerHeight / 2 -28;
    if(window.innerWidth < 780) {
      this.radius = 75;
    } else {
      this.radius = 120;
    }
    this.debounce(1000, this.initSvg);
  }

  public debounce(idle: any, action: any): any {
    this.last && clearTimeout(this.last);
    this.last = setTimeout(action, idle)
  }

  public initBg(): void {
    let rect = this.draw.rect("100%", "100%")
    let gradient = this.draw.gradient('radial', function(stop: any) {
      stop.at({ offset: 0, color: '#52669a', opacity: 0 })   // -> first
      stop.at({ offset: 0.5, color: '#243a67', opacity: 0.5 }) // -> second
      stop.at({ offset: 1, color: '#1b2336', opacity: 0 })   // -> third
    })
    rect.radius(0.5).fill(gradient)

  }

  public circleButton(r: any): void {
    var group = this.draw.nested()

    let circle = group.circle(r).center(this.windowHalfX, this.windowHalfY).fill('#fff9b1');
    let text = group.text("吃什么").font({
      family: 'Helvetica',
      size:  20,
      fill:  "#c0c0c0"
    }).center(this.windowHalfX, this.windowHalfY);
    // group.add(circle);
    // group.add(text);

    // group.mouseover(function() { 
    //   this.first().animate().scale(1.1, 1.1);
    // })
    // group.mouseout(function() {
    //   this.first().animate().scale(1, 1);
    // })
    group.mousedown(function(){
      group.first().fill({ color: '#ffde00' });
    })
    group.mouseup(function(){
      group.first().fill({ color: '#fff9b1' });
    })
    

  }
  public initStar(num: any ): void {
    for (let i = 0; i < num; i++ ) {
      let circle = this.draw.circle(2)
                            .center(Math.random() * window.innerWidth, Math.random() * window.innerHeight)
                            .fill({color: '#fff9b1',opacity: 0.7 });
    }
  }

  public initCircle(r: any ): void {
    for (let i = 1; i < 4; i++ ) {
      let circle = this.draw.circle(r * 2 * i)
                            .center(this.windowHalfX, this.windowHalfY)
                            .fill('none')
                            .stroke({ width: 2, color: '#fff9b1' });

      this.texts.forEach( (item: any, index: any) =>{
        if(index < 7 * i && index >= 7 * (i-1) ) {
          this.drawText(r * i, item);
        }
      })
    }
  }

  public drawText(r: any, text: any): void {
    const color: any = {
      0: '#fff',
      1: '#ffde00',
      2: '#ff6e6e',
      3: '#48f0ff'
    }
    let a: any = this.windowHalfX;
    let b: any = this.windowHalfY;

    let cx = this.T(-r, r) + this.windowHalfX;
    let sign = this.T(-1, 1) > 0 ? 1:-1
    let cy = sign * Math.sqrt( r*r - Math.pow(cx - this.windowHalfX ,2)) + this.windowHalfY;

    let cx1 = cx + 120;
    let sign1 = this.T(-1, 1) > 0 ? 1:-1
    let cy1 =  sign1 * Math.sqrt( r*r - Math.pow(cx1 - this.windowHalfX ,2)) + this.windowHalfY;

    // var path = this.draw.path(`M${cx} ${cy} A ${r} ${r}, 0, 0, 1, ${cx - r} ${cy - r}`)
    // path.fill('none');
    // path.stroke({ color: '#f06', width: 4, linecap: 'round', linejoin: 'round' })

   // var text = this.draw.text(text)

   // var path = `M${cx} ${cy} A ${r} ${r}, 0, 1, 0, ${cx - r } ${cy - r}`;

   // text.path(path).font({ size: 14, family: 'Verdana',fill:"#fff" })

   this.draw.text(text).font({
         family: 'Helvetica',
         size:  14,
         fill:  "#fff"
       }).center(cx, cy + 20);
  
    let circle: any = this.draw.circle(12).center(cx, cy).attr({ fill: color[Math.floor(Math.random() * 3) ] })

    if ( this.T(-1, 4) > 0 ) {
      return;
    } 

    let random = 2000 + Math.random() * 2000;

    let circle1: any  = this.draw.circle(12).center(cx, cy).attr({ fill: '#fff' ,opacity: 0.7})
    circle1.animate(random, '>').radius(12 * 2).fill({ color: '#fff', opacity: 0 }).loop()

    let circle2: any  = this.draw.circle(12 * 2 ).center(cx, cy).attr({ fill: '#fff' ,opacity: 0.7})
    circle2.animate(random , '>').radius(12 * 4).fill({ color: '#fff', opacity: 0 }).loop()
  }

  public T(e: any, t: any): any {
      return Math.random() * (t - e) + e
  }
}
</script>

<style lang="scss">
    .circle-press {
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
 
    #drawing {
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: 10004;
      cursor: pointer;
    }
</style>
