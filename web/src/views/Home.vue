<template>
  <div class="home">
    <img alt="Vue logo" src="../assets/logo.png">
    <HelloWorld msg="Welcome to Your Vue.js + TypeScript App"/>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import HelloWorld from '@/components/HelloWorld.vue'; // @ is an alias to /src
import { getVideo } from '@/api/api.ts';

@Component({
  components: {
    HelloWorld,
  },
})
export default class Home extends Vue {
  private list: any = [];

  public mounted(): void {
    this.getVideo();
  }
  public getVideo(): void {
    getVideo().then((res: any) => {
      const data = res.data;
      if (data.count) {
        this.list = data.rows;
        this.list.forEach((item: any) => {
          item.status = item.status + '';
        });
      }
    });
  }
}
</script>
