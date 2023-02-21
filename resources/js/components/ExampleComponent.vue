<template>
    <nav class="navbar bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <!-- <img src="/docs/5.2/assets/brand/bootstrap-logo.svg" alt="Logo" width="30" height="24"
                    class="d-inline-block align-text-top"> -->
                ProjKonnect
            </a>
        </div>
    </nav>

    <div class="card w-50 mx-auto mt-5 text-center">

        <span class="alert alert-success" role="alert" v-if="success"> Password has been resetted successfully .</span>

        <span class="alert alert-danger" role="alert" v-if="error"> {{ errorMessage }} .</span>



        <form class="mt-4">
            <div class="mb-3">
                <label for="password" class="form-label">Enter Password</label>
                <input v-model="form.password" type="password" class="form-control mx-auto w-75" id="password"
                    placeholder="Password">
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input v-model="form.confirmPassword" class="form-control mx-auto w-75" id="confirmPassword" type="password"
                    placeholder="Confirm Password" rows="2">
                <small v-if="passwordsDoNotMatch || passwordsEmpty" class="text-danger">Passwords do not match.</small>
            </div>
            <button @click.prevent="changePassword"
                class="btn btn-primary justify-content-center mb-1 align-content-center">Submit</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                password: "",
                confirmPassword: "",
                token: "",
                email: ""
            },
            passwordsDoNotMatch: false,
            passwordsEmpty: false,
            missingdata: false,
            success: false,
            error: false,
            errorMessage: ""
        };
    },

    methods: {
        async changePassword() {
            try {

                this.passwordsDoNotMatch = false;
                this.passwordsEmpty = false;
                this.missingdata = false;
                this.error = false
                this.success = false

                if (this.form.password === "" || this.form.confirmPassword === "") {
                    this.passwordsEmpty = true;
                    return
                } else if (this.form.password !== this.form.confirmPassword) {
                    this.passwordsDoNotMatch = true;
                    return
                }

                let urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('token') == null || urlParams.has('email') == null) {
                    this.missingdata = true

                    return
                }

                this.form.token = urlParams.get('token')
                this.form.email = urlParams.get('email')

                let response = await axios.post('/api/user_reset_password', this.form)

                if (response.data.code === 3) {
                    this.error = true
                    this.errorMessage = response.data.error
                    return
                }

                if (response.data.code === 1) {
                    this.success = true
                    return
                }



            } catch (err) {
                console.log(err)
            }

        },
    },
};
</script>
