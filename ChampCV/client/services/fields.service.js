define(function () {
    /**
    * Saves the possible fields in resume metadata
    **/
    function FieldsService() {
        var _fields = [{ id: 'id_1', name: "Full-Stack Developer"},
                       { id: 'id_2', name: "Front-end Developer"},
                       { id: 'id_3', name: "Back-end Developer"},
                       { id: 'id_4', name: "UX/UI"},
                       { id: 'id_5', name: "BI"},
                       { id: 'id_6', name: "QA"},
                       { id: 'id_7', name: "DBA"},
                       { id: 'id_8', name: "IT"}
        ];

        return {
            /*
                Return a new reference of the list
            */
            getFieldsList: function () {
                return this.copy(_fields);
            },
            /*
                return a field object according to ID
            */
            getFieldByID: function (id) {
                var wantedField;
                for(var i = 0; i < _fields.length; i++){
                    if (_fields[i].id == id) {
                        wantedField = _fields[i];
                        break;
                    }
                }
                return wantedField;
            },
            /*
                Copy the list
            */
            copy: function () {
                var list = [];
                _fields.forEach(function (field) {
                    list.push(angular.merge({}, field));
                });

                return list;
            }
                    
         }
       
    }

    return FieldsService;
});