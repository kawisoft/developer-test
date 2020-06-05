<template>
    <div>
        <b-button @click="modalShow = !modalShow">Add Column</b-button>

        <b-modal
            id="modal-prevent-closing"
            ref="modal"
            title="Adding a new column"
            @show="resetModal"
            @hidden="resetModal"
            @ok="handleOk"
            v-model="modalShow"
        >
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group
                    :state="nameState"
                    label="Column Name"
                    label-for="name-input"
                    invalid-feedback="Column Name is required"
                >
                    <b-form-input
                        id="name-input"
                        v-model="name"
                        :state="nameState"
                        required
                    ></b-form-input>
                </b-form-group>
            </form>
        </b-modal>
    </div>
</template>

<script>
    export default {
        name: "AddColumn",
        props: ['popUp',],

        data() {
            return {
                name: '',
                nameState: null,
                submittedNames: [],
                modalShow: false
            }
        },
        methods: {
            checkFormValidity() {
                const valid = this.$refs.form.checkValidity()
                this.nameState = valid
                return valid
            },
            resetModal() {
                this.name = ''
                this.nameState = null
            },
            handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault()
                // Trigger submit handler
                this.handleSubmit()
            },
            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    return
                }
                // Push the name to submitted names
                this.submittedNames.push(this.name)
                this.$emit('captured-column-name', this.name)
                // Hide the modal manually
                this.$nextTick(() => {
                    this.$bvModal.hide('modal-prevent-closing')
                })
            }
        }
    }
</script>

<style scoped>

</style>
