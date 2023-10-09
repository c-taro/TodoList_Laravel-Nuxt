<template>
  <v-app>
    <v-card tile>
      <v-toolbar flat d-flex flex-row>
        <div style="width:44px;margin:10px">
          <ApplicationLogo class="fill-current black--text" />
        </div>
        <v-tabs v-model="tab" align-with-title>
        <!-- Tabs https://v2.vuetifyjs.com/ja/components/tabs/#section-4f7f304465b9 -->
          <v-tabs-slider color="primary"></v-tabs-slider>
          <v-tab :to="localePath('/board/'+board.id)" v-for="board in $store.state.boards" :key="board.id">
            {{ board.title }}
          </v-tab>
          <NuxtLink :to="localePath('/board/create')" class="text-decoration-none tab">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-tab v-bind="attrs" v-on="on" style="height:100%">
                  <svg-icon type="mdi" :path="create_icon"></svg-icon>
                </v-tab>
              </template>
              <span>{{ $t('tooltip.create_a_board') }}</span>
            </v-tooltip>
          </NuxtLink>
        </v-tabs>

        <v-spacer></v-spacer>

        <v-menu offset-y :close-on-content-click="false">
        <!-- VuetifyでQiita風UIを作る - チーム切り替えメニュー https://qiita.com/totto357/items/31879483187a1db410b9 -->
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon large v-bind="attrs" v-on="on">
              <!-- ボタンのアイコン化: https://v2.vuetifyjs.com/ja/components/floating-action-buttons/ -->
              <svg-icon type="mdi" :path="account_icon" :size="35"></svg-icon>
              <!-- アイコンのサイズ: https://qiita.com/FJHoshi/items/9ee7af43f8b7e10faf66 -->
            </v-btn>
          </template>

          <!-- https://v2.vuetifyjs.com/ja/components/lists/#section-305d306e4ed6 -->
          <v-list>
            <v-list-item @click="accountMenu('logout')">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t('account_menu.log_out') }}</v-list-item-title>
            </v-list-item>
            <v-list-group v-for="(item, index) in account_menu" :key="index" :prepend-icon="item.icon" no-action>
            <!-- ネストしたメニューをインデントする場合は、no-actionプロパティを指定します。 https://qiita.com/rubytomato@github/items/784a0eb013a9de1937bd -->
            <template v-slot:activator>
              <v-list-item-content>
                <v-list-item-title>{{ itemTitle(index) }}</v-list-item-title>
              </v-list-item-content>
            </template>
            <v-list-item v-for="locale in availableLocales" :key="locale.code" @click="() => changeLocale(locale.code, item)" link>
            <!-- https://zenn.dev/toshinobu/articles/233aa9b1b65115 -->
              <v-list-item-content>
                <v-list-item-title>{{ locale.name }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-group>
          </v-list>
          <!-- Menus https://v2.vuetifyjs.com/ja/components/menus/#section-4f7f304465b9 -->
        </v-menu>
      </v-toolbar>

      <v-sheet class="d-flex">
        <v-tabs-items v-model="tab">
          <v-tab-item v-for="board in $store.state.boards" :key="board.id" :value="localePath('/board/'+board.id)">
            <v-card flat>
              <v-card-text class="text-h5 font-weight-bold black--text">{{ board.title }}</v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item :value="localePath('/board/create')">
            <v-card flat>
              <v-card-text class="text-h5 font-weight-bold black--text">{{ $t('page.board_creation') }}</v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-sheet>
    </v-card>
    <v-main>
      <Nuxt></Nuxt>
    </v-main>
  </v-app>
</template>

<script>
  import SvgIcon from "@jamescoyle/vue-icon";
  import { mdiAccountCircle, mdiTranslate, mdiPlus } from "@mdi/js";
  export default {
    mounted(){
      this.$nuxt.$on('getBoards', this.getBoards);
    },
    created(){
      this.getBoards();
    },
    name: "my-component",
    components: {
      SvgIcon,
    },
    data() {
      return {
        create_icon: mdiPlus,
        account_icon: mdiAccountCircle,
        tab: null,
        account_menu: [
          {
            item: this.$t('account_menu.change_language'),
            icon: mdiTranslate,
          }
        ],
      };
    },
    computed: {
      availableLocales() {
        return this.$i18n.locales.filter((i) => i.code !== this.$i18n.locale)
      },
    },
    methods: {
      async getBoards(){
        const res = await this.$axios.get('/api/boards');
        this.$store.commit('setBoards', res.data);
        this.tab = '/'+this.$i18n.locale+'/board/'+this.$route.params.boardId;
      },
      itemTitle(index) {
        return this.$t(this.account_menu[index].item);
      },
      // https://zenn.dev/toshinobu/articles/233aa9b1b65115
      async changeLocale(locale, parentItem) {
        this.$i18n.setLocaleCookie(locale)
        await this.$router.push(this.switchLocalePath(locale));// https://medium.com/js-dojo/connect-i18n-and-vee-validate-nuxt-js-i18n-and-vee-validate-works-greate-together-d086edd4ca22
        parentItem.item = 'account_menu.change_language';
      },
      async accountMenu(action){
        if(action === 'logout'){
          await this.$axios.get("/sanctum/csrf-cookie");
          await this.$axios.post('/logout');
          this.$auth.logout();
        }
      },
    }
  };
</script>