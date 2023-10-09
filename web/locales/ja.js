import validationJa from 'vee-validate/dist/locale/ja'
const localeJa = {
    validation: validationJa.messages,
    model: {
      board: {
        title: "ボード名"
      },
      task: {
        tasks: "タスク",
        title: "タスク名",
        content: "説明",
        person_in_charge: "担当者"
      },
      setting: {
        settings: "設定"
      },
      trash: "ゴミ箱"
    },
    page: {
      login: "ログイン",
      forgot_password: "パスワードを忘れた場合",
      password_reset: "パスワードリセット",
      dashboard: "ダッシュボード",
      board_creation: "ボード作成",
      board_setting: "ボード設定",
      task_editing: "タスク編集",
      task_details: "タスク詳細",
      task_creation: "タスク作成",
      trash: "ゴミ箱",
      task_list: "タスク一覧",
    },
    button: {
      log_in: "ログイン",
      register: "登録",
      send_password_reset_link: "パスワードリセットメールを送信",
      reset_password: "パスワードのリセット",
      create: "作成",
      update: "更新",
      delete: "削除",
      cancel: "キャンセル",
      delete_this_board: "このボードを削除する"
    },
    table: {
      rows_per_page: "表示件数:"
    },
    form: {
      name: "名前",
      email: "メールアドレス",
      password: "パスワード",
      password_confirmation: "パスワード確認"
    },
    modal: {
      do_you_want_to_delete_the_board_XXX: "ボード「{_field_}」を削除しますか？",
    },
    tooltip: {
      create_a_board: "ボードを作成する",
      open_the_trash_box: "ゴミ箱を開く",
      open_the_board_settings: "ボード設定を開く",
      create_a_task: "タスクを作成する",
      edit: "編集する",
      delete: "削除する",
      open_details: "詳細を開く",
      restore: "復元する",
    },
    account_menu: {
      log_out: "ログアウト",
      change_language: "言語の切り替え"
    },
    link: {
      forgot_your_password: "パスワードを忘れた場合",
      create_an_account: 'アカウント作成',
      already_registered: "既に登録済みの場合"
    },
    success: {
      account_was_created_successfully: "アカウントが正常に作成されました。",
      email_was_sent_successfully: "メールが正常に送信されました！",
      password_was_changed_successfully: "パスワードが正常に更新されました！"
    },
    error: {
      XXX_error_occurred_please_try_again_later: "{_field_}エラーが発生しました。後でもう一度お試しください。",
      an_error_occurred_please_try_again_later: "エラーが発生しました。後でもう一度お試しください。",
      the_task_is_not_deleted: "タスクは削除されていません。",
      the_given_data_is_invalid: "送信されたデータは無効です。",
      the_data_you_requested_could_not_be_found: "要求されたデータは見つかりませんでした。",
      the_requested_url_was_not_found_on_this_server: "このサーバーに要求されたURLは見つかりませんでした。",
      an_unexpected_error_occurred_while_processing_your_request: "リクエストの処理中に予期しないエラーが発生しました。",
      this_action_is_unauthorized: "このアクションは許可されていません。",
    },
    message: {
      forgot_your_password_no_problem_just_let_us_know_your_email_address_and_we_will_email_you_a_password_reset_link_that_will_allow_you_to_choose_a_new_one: "パスワードをお忘れですか？ご登録時のメールアドレスを入力頂ければ、パスワードリセットのためのリンクをメールで送信いたします。",
      getting_tasks: "タスクを取得中...",
      deleting_a_board_also_deletes_all_tasks_in_that_board_and_they_cannot_be_restored: "ボードを削除するとそのボードの中にある全てのタスクも削除され、復元ができなくなります。"
    }
};
export default localeJa;