export const commons = {
    methods: {
        /**
         * Renderiza los errores
         * 
         * @param {*} list_err 
         * @param {*} errors 
         */
        parseErrors(list_err, errors){
            this.clearValues(list_err);
            for(let index in errors){
                if(list_err.hasOwnProperty(index)){
                    list_err[index] = errors[index][0];
                }
            }
        },
        /**
         * Limpia los errores 
         * 
         * @param {*} list_err 
         */
        clearValues(list_err){
            for(let i in list_err){
                list_err[i] = '';
            }
        },

    },
};