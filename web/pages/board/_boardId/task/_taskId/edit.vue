<template>
   <ValidationObserver ref="form">
    <form @submit.prevent="update">
      <v-container class="mt-5">
        <v-row justify="start">
          <p class="id-font grey--text ml-10">#{{ $route.params.taskId }}</p>
        </v-row>
        <v-row class="mt-10" justify="center">
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n7 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%"><!-- nuxt.jsでフォームにバリデーションをつける。 https://qiita.com/fussy113/items/2693f3911f74b4b575a2 -->
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row>
          <validation-provider vid="title" rules="required|max:255" :name="$t('model.task.title')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="task.title" :placeholder="$t('model.task.title')" :error-messages="errors[0]" outlined class="title-font centered-input"></v-text-field>
            <!-- https://v2.vuetifyjs.com/ja/components/text-fields/ -->
            <!-- https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->
          </validation-provider>
        </v-row>
        <v-row class="mt-5">
          <validation-provider vid="content" rules="max:16384" :name="$t('model.task.content')" v-slot="{ errors }" style="width:100%">
            <v-textarea v-model="task.content" :label="$t('model.task.content')" :error-messages="errors[0]" outlined></v-textarea>
          </validation-provider>
        </v-row>
        <v-row>
          <validation-provider vid="person_in_charge" rules="max:255" :name="$t('model.task.person_in_charge')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="task.person_in_charge" id="person_in_charge" :label="$t('model.task.person_in_charge')" :error-messages="errors[0]" outlined></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="end">
          <v-btn type="submit" color="primary" dark large>{{ $t('button.update') }}</v-btn>
        </v-row>
      </v-container>
    </form>
  </ValidationObserver>
</template>
<script>
  export default {
    layout: "authenticated",
    head() {
      return {
        title: this.$t('page.task_editing'),
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
      },
      async update() {
        try{
          this.$refs.form.setErrors({general: ''});
          await this.$axios.get("/sanctum/csrf-cookie");
          await this.$axios.put(`/api/board/${this.$route.params.boardId}/task/${this.$route.params.taskId}`, this.task);
          this.$router.push(this.localePath('/board/' + this.$route.params.boardId));
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if (status === 422 && data?.errors) {
              this.$refs.form.setErrors(data.errors);
            }else if(data?.detail){
              this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});
            }else{
              this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
            }
          }else{
            this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      }
    }
  }
</script>