<template>
  <form>
    <v-container class="mt-5">
      <v-row justify="start">
        <p class="id-font grey--text ml-10">#{{ $route.params.taskId }}</p>
        <!-- nuxtで画面遷移時のパラメータ受け渡し(params, query) https://codelikes.com/nuxt-query-or-params/#toc1 -->
        <!-- Nuxt.js + Vue + axios でURLのid (パスパラメータ) を利用してhttpリクエストする例 https://qiita.com/YumaInaura/items/0525b06bf261710f9b00 -->
      </v-row>
      <v-row class="mt-5">
        <ValidationObserver ref="form">
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%;">
              {{ errors[0] }}
            </div>
          </validation-provider>
        </ValidationObserver>
      </v-row>
      <v-row>
        <v-text-field v-model="task.title" :placeholder="$t('model.task.title')" filled readonly outlined class="title-font centered-input"></v-text-field>
        <!-- https://v2.vuetifyjs.com/ja/components/text-fields/ -->
        <!-- https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->
      </v-row>
      <v-row class="mt-5">
        <v-textarea v-model="task.content" :label="$t('model.task.content')" filled readonly outlined></v-textarea>
      </v-row>
      <v-row>
        <v-text-field v-model="task.person_in_charge" id="person_in_charge" :label="$t('model.task.person_in_charge')" filled readonly outlined></v-text-field>
      </v-row>
    </v-container>
  </form>
</template>
<script>
  export default {
    layout: "authenticated",
    head() {
      return {
        title: this.$t('page.task_details'),
      };
    },
    props: {
      id: Number,//自分はStringだと動かなかったけどNumberで動く人もいるらしい
    },
    data(){
      return{
        task: {}
      }
    },
    mounted() {
      this.getTask();
    },
    methods: {
      async getTask() {
        try{
          const res = await this.$axios.get(`/api/board/${this.$route.params.boardId}/task/${this.$route.params.taskId}`);
          this.task = res.data;
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(data?.detail){
              this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});
            }else{
              this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
            }
          }else{
            this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      }
    },
  }
</script>