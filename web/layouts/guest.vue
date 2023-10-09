<template>
  <v-app :style="{ background: $vuetify.theme.themes.light.background }">
  <!-- 【Vuetify】メインコンテンツの背景色を変更する https://qiita.com/tutti2612/items/ffc0b14266065365f11a -->
    <v-container>
      <v-row justify="center">
        <v-col style="max-width: 500px;">
          <v-container class="mx-auto my-12">
            <v-row justify="center"><!-- https://www.fuwamaki.com/article/61 -->
              <v-col cols="3">
                <ApplicationLogo class="w-20 h-20 fill-current grey--text text--darken-1" />
                <!-- How to change Vuetify v-icon color in css https://stackoverflow.com/questions/60903847/how-to-change-vuetify-v-icon-color-in-css -->
              </v-col>
            </v-row>
            <v-card class="mx-auto px-5 rounded-lg">
              <Nuxt></Nuxt>
            </v-card>
          </v-container>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
import NuxtLogo from '../components/NuxtLogo.vue'// https://github.com/filippetrovicAT/breeze-nuxt
export default {
  components: { NuxtLogo }
  // ,
  // middleware: 'guest'// 【nuxt】middlewareについて https://qiita.com/yama-t/items/97871a4c2f1cd8325078
}
</script>