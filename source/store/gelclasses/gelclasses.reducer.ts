import { List } from "immutable";
import { recordify } from "typed-immutable-record";
import { GELCLASSES_INIT, GELCLASSES_SAVE, GELCLASSES_RECEIVED, GELCLASSES_RESET, GELCLASSES_SAVE_WITHIDS } from "../../constants";
import { GELCLASSES_INITIAL_STATE } from "./gelclasses.initial-state";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "./gelclasses.types";

export function gelclassesReducer(state: IGelClassRecords = GELCLASSES_INITIAL_STATE, action): IGelClassRecords {

    switch (action.type) {
        case GELCLASSES_RECEIVED:
            let receivedGelClasses = Array<IGelClassRecord>();
            let i=0;
            action.payload.gelclasses.forEach(gelclass => {
                receivedGelClasses.push(recordify<IGelClass, IGelClassRecord>({ id: gelclass.id, name: gelclass.name, category: gelclass.category, selected: false}));
                i++;
            });
            return List(receivedGelClasses);

        case GELCLASSES_SAVE:
        return state.withMutations(function(list) {
            if (action.payload.selected_id >= 0)
                list.setIn([action.payload.selected_id, "selected"], false);
            if (action.payload.new_selected_choice_id >= 0)
                list.setIn([action.payload.new_selected_choice_id, "selected"], true);
        });

        case GELCLASSES_SAVE_WITHIDS:
        return state.withMutations(function(list) {

            if (action.payload.new_selected_choice_id > 0){

                const indexOfListingToUpdate = list.findIndex(listing => {
                    return listing.get('id') === action.payload.new_selected_choice_id;});

                list.setIn([indexOfListingToUpdate, "selected"], true);
            }
            
            if (action.payload.selected_id > 0){
                const indexOfListingToUpdate2 = list.findIndex(listing => {
                    return listing.get('id') === action.payload.selected_id;});

                list.setIn([indexOfListingToUpdate2, "selected"], false);
            }
        });

        case GELCLASSES_RESET:
        let resetedGelClasses = Array<IGelClassRecord>();
        action.payload.gelclasses.forEach(gelclass => {
                resetedGelClasses.push(recordify<IGelClass, IGelClassRecord>({ id: gelclass.id, name: gelclass.name, category: gelclass.category, selected: false}));
        });
        return List(resetedGelClasses);

        case GELCLASSES_INIT:
            return GELCLASSES_INITIAL_STATE;
        default: return state;

    }
};
