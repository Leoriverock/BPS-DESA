Vtiger_Edit_Js("Relationship_Edit_Js", {}, {
    
    registerEvents: function() {
        this._super();
    },
    registerBasicEvents: function(container) {
        this._super(container);
        
        jQuery('.createReferenceRecord').css('display', 'none');
    }
});