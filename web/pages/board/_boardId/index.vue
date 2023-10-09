<template>
  <v-container class="mx-auto">
    <v-row justify="end" class="ma-5">
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-bind="attrs" v-on="on" :to="localePath({ name: 'board-boardId-trash', params: {boardId: $route.params.boardId} })" class="mx-2" fab small color="grey" dark elevation="0">
            <svg-icon type="mdi" :path="trash_icon"></svg-icon>
            <!-- ボタンのアイコン化: https://v2.vuetifyjs.com/ja/components/floating-action-buttons/ -->
            <!-- アイコンのサイズ: https://qiita.com/FJHoshi/items/9ee7af43f8b7e10faf66 -->
          </v-btn>
        </template>
        <span>{{ $t('tooltip.open_the_trash_box') }}</span>
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-bind="attrs" v-on="on" :to="localePath({ name: 'board-boardId-setting', params: {boardId: $route.params.boardId} })" class="mx-2" fab small color="grey" dark elevation="0">
            <svg-icon type="mdi" :path="setting_icon"></svg-icon>
          </v-btn>
        </template>
        <span>{{ $t('tooltip.open_the_board_settings') }}</span>
      </v-tooltip>
    </v-row>
    <v-row>
      <ValidationObserver ref="error">
        <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
          <div v-show="errors.length" class="mt-n10 ml-5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%;">
            {{ errors[0] }}
          </div>
        </validation-provider>
      </ValidationObserver>
    </v-row>
      <!-- https://v2.vuetifyjs.com/ja/components/data-tables/#section-4f7f304465b9 -->
      <v-data-table
        :headers="headers"
        :items="tasks"
        :items-per-page="5"
        :footer-props="{
          'items-per-page-options':[5, 10, 50, 100, -1],
          'items-per-page-text': this.$t('table.rows_per_page'),
        }"
        class="elevation-1"
      >
        <template v-slot:[`header.actions`]>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
            <!-- 【Vuetify】v-data-tableの特定の列のヘッダーをv-slotを使用してテキスト以外の要素を入れる方法 https://qiita.com/natsume4/items/76fe3eb5994c88cf5459 -->
              <v-btn v-bind="attrs" v-on="on" :to="localePath({ name: 'board-boardId-task-create', params: {boardId: $route.params.boardId} })" class="mx-2" fab small color="primary" elevation="0">
                <svg-icon dark type="mdi" :path="create_icon"></svg-icon>
              </v-btn>
            </template>
            <span>{{ $t('tooltip.create_a_task') }}</span>
          </v-tooltip>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <!-- 'v-slot' directive doesn't support any modifierとエラーが出たときの対処方法。 https://qiita.com/pokoTan2525/items/c698457d2473dab0868f -->
          <!-- Nuxt × Vuetifyの状況下でv-btnをnuxt-linkにして使いたい https://qiita.com/checche/items/810a71d8f1f93b251aae -->
          <!-- Vuetify.js のデータテーブルでボタンを使用する https://www.paveway.info/entry/2021/07/23/vuetifyjs_v-data-table_button -->
          <!-- 'v-slot' directive doesn't support any modifierとエラーが出たときの対処方法。 https://qiita.com/pokoTan2525/items/c698457d2473dab0868f -->
          <!-- <v-btn v-bind="attrs" v-on="on" :to="localePath({ name: 'board-boardId-task-taskId', params: {boardId: $route.params.boardId, taskId: item.id} })" v-show=show_action_icons> -->
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" icon color="primary" :to="localePath({ name: 'board-boardId-task-taskId', params: {boardId: $route.params.boardId, taskId: item.id} })">
                <svg-icon type="mdi" :path="show_icon"></svg-icon>
              </v-btn>
            </template>
            <span>{{ $t('tooltip.open_details') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" icon color="primary" :to="localePath({name: 'board-boardId-task-taskId-edit', params: {boardId: $route.params.boardId, taskId: item.id} })">
                <svg-icon type="mdi" :path="edit_icon"></svg-icon>
              </v-btn>
            </template>
            <span>{{ $t('tooltip.edit') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" icon color="red" dark @click="deleteTask(item.id)">
                <svg-icon type="mdi" :path="delete_icon"></svg-icon>
              </v-btn>
            </template>
            <span>{{ $t('tooltip.delete') }}</span>
          </v-tooltip>
        </template>
        <template v-slot:[`footer.page-text`]="props">
            {{ props.pageStart }}~{{ props.pageStop }} / {{ props.itemsLength }}
        </template>
        <!-- https://qiita.com/00__/items/4dbe795a392c5d1fa6a4 -->
      </v-data-table>
  </v-container>
</template>
<script>
  import SvgIcon from "@jamescoyle/vue-icon";
  import { mdiEye, mdiSquareEditOutline, mdiCloseThick, mdiPlusThick, mdiTrashCan, mdiCog } from "@mdi/js";
  export default {
    name: "my-component",
    components: {
      SvgIcon,
    },
    head() {
      return {
        title: this.$t('page.task_list'),
      };
    },
    layout: "authenticated",
    data () {
      return {
        setting_icon: mdiCog,
        trash_icon: mdiTrashCan,
        create_icon: mdiPlusThick,
        show_icon: mdiEye,
        edit_icon: mdiSquareEditOutline,
        delete_icon: mdiCloseThick,
        headers: [
          {text: '#', value: 'id', sortable: true, width: '10%', class: "primary white--text font-weight-bold rounded-tl-lg"},
          // https://vuetifyjs.com/ja/styles/border-radius/#pill-3068-circle
          // 【Vuetify】これだけ見れば済む色の設定方法まとめ https://qiita.com/senth/items/10e9980f21174cf599a1
          { text: this.$t('model.task.title'), value: 'title', sortable: true, width: '55%', class: "primary white--text font-weight-bold"},
          { text: this.$t('model.task.person_in_charge'), value: 'person_in_charge', sortable: true, width: '20%', class: "primary white--text font-weight-bold"},
          { text: '', value: "actions", align: 'end', sortable: false, width: '147px', class: "primary white--text font-weight-bold rounded-tr-lg"},// アイコンが改行されない最小幅が147px
        ],
        tasks: [{id: null, title: this.$t('message.getting_tasks'), person_in_charge: null}],
      }
    },
    mounted() {
      this.getTasks();
    },
    methods: {
      async getTasks() {
        try{
          const res = await this.$axios.get(`/api/board/${this.$route.params.boardId}/tasks`);
          this.tasks = res.data;
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(data?.detail){
              this.$refs.error.setErrors({general: this.$t('error.' + data.detail)});
            }else{
              this.$refs.error.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
            }
          }else{
            this.$refs.error.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      },
      async deleteTask(taskId) {
        try{
          this.$refs.error.setErrors({general: ''});
          await this.$axios.delete(`/api/board/${this.$route.params.boardId}/task/${taskId}`);
          const index = this.tasks.findIndex(task => task.id === taskId);
          if (index !== -1) {
            this.tasks.splice(index, 1);
          }
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(data?.detail){
              this.$refs.error.setErrors({general: this.$t('error.' + data.detail)});
            }else{
              this.$refs.error.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
            }
          }else{
            this.$refs.error.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      }
    }
  };
</script>