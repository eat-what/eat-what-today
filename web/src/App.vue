<template>
  <div id="app">
    <div id="nav">
      <router-link to="/">Home</router-link> |
      <router-link to="/about">About</router-link> |
      <router-link to="/CirclePress">CirclePress</router-link>
    </div>
    <router-view/>
  </div>
</template>

<script>

export default {
  name: 'App',
}
</script>

<style lang="scss">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
#nav {
    position: relative;
    z-index: 1003;
    height: 56px;
    line-height: 56px;
    text-align: center;
    background-color: #18223d;
    text-align: center;
  a {
    text-decoration: none;
    font-weight: bold;
    color: rgba(255, 255, 255, 0.7);
    &.router-link-exact-active {
      color: rgba(255, 255, 255, 1);
    }
  }
}
</style>
