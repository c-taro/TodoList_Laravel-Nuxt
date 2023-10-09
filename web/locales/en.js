import validationEn from 'vee-validate/dist/locale/en'
const localeEn = {
    validation: validationEn.messages,
    model: {
      board: {
        title: "Title"
      },
      task: {
        tasks: "Tasks",
        title: "Title",
        content: "Content",
        person_in_charge: "Person in charge"
      },
      setting: {
        settings: "Settings"
      },
      trash: "Trash"
    },
    page: {
      login: "Login",
      forgot_password: "Forgot Password",
      password_reset: "Password Reset",
      dashboard: "Dashboard",
      board_creation: "Board Creation",
      board_setting: "Board Setting",
      task_editing: "Task Editing",
      task_details: "Task Details",
      task_creation: "Task Creation",
      trash: "Trash",
      task_list: "Task List",
    },
    button: {
      log_in: "Log in",
      register: "Register",
      send_password_reset_link: "Send Password Reset Link",
      reset_password: "Reset Password",
      create: "Create",
      update: "Update",
      delete: "Delete",
      cancel: "Cancel",
      delete_this_board: "Delete this board"
    },
    table: {
      rows_per_page: "Rows per page:"
    },
    form: {
      name: "Name",
      email: "E-mail",
      password: "Password",
      password_confirmation: "Password confirmation"
    },
    modal: {
      do_you_want_to_delete_the_board_XXX: "do you want to delete the board \"{_field_}\"?",
    },
    tooltip: {
      create_a_board: "Create a board",
      open_the_trash_box: "Open the trash box",
      open_the_board_settings: "Open the board settings",
      create_a_task: "Create a task",
      edit: "Edit",
      delete: "Delete",
      open_details: "Open details",
      restore: "Restore",
    },
    account_menu: {
      log_out: "Log out",
      change_language: "Change Language"
    },
    link: {
      forgot_your_password: "Forgot your password?",
      create_an_account: 'Create account',
      already_registered: "Already registered?"
    },
    success: {
      account_was_created_successfully: "Account was created successfully!",
      email_was_sent_successfully: "Email was sent successfully!",
      password_was_changed_successfully: "Password was changed successfully!"
    },
    error: {
      XXX_error_occurred_please_try_again_later: "{_field_} error occurred. Please try again later.",
      an_error_occurred_please_try_again_later: "An error occurred. Please try again later.",
      the_task_is_not_deleted: "The task is not deleted.",
      the_given_data_is_invalid: "The given data is invalid.",
      the_data_you_requested_could_not_be_found: "The data you requested could not be found.",
      the_requested_url_was_not_found_on_this_server: "The requested URL was not found on this server.",
      an_unexpected_error_occurred_while_processing_your_request: "An unexpected error occurred while processing your request.",
      this_action_is_unauthorized: "This action is unauthorized.",
    },
    message: {
      forgot_your_password_no_problem_just_let_us_know_your_email_address_and_we_will_email_you_a_password_reset_link_that_will_allow_you_to_choose_a_new_one: "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.",
      getting_tasks: "getting tasks ...",
      deleting_a_board_also_deletes_all_tasks_in_that_board_and_they_cannot_be_restored: "Deleting a board also deletes all tasks in that board and they cannot be restored.",
    }
};
export default localeEn;