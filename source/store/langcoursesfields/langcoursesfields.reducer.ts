import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { LANGCOURSEFIELDS_INIT, LANGCOURSEFIELDS_RECEIVED, LANGCOURSEFIELDS_SELECTED_SAVE, LANGCOURSES_ORDER_SAVE, LANGCOURSEFIELDS_SELECTED_SAVE_WITHIDS } from "../../constants";
import { LANGCOURSE_FIELDS_INITIAL_STATE } from "./langcoursesfields.initial-state";
import { ILangCourseField, ILangCourseFieldRecord, ILangCourseFieldRecords } from "./langcoursesfields.types";

export function langcourseFieldsReducer(state: ILangCourseFieldRecords = LANGCOURSE_FIELDS_INITIAL_STATE, action): ILangCourseFieldRecords {
    switch (action.type) {
        case LANGCOURSEFIELDS_RECEIVED:
            let newLangCourseFields = Array<ILangCourseFieldRecord>();
            let i = 0;
            action.payload.langcourseFields.forEach(langcourseField => {
                newLangCourseFields.push(recordify<ILangCourseField, ILangCourseFieldRecord>
                  ({ id: langcourseField.id, name: langcourseField.name, selected: false, order_id: 0 }));
                i++;
            });
            return List(newLangCourseFields);

        case LANGCOURSEFIELDS_SELECTED_SAVE:
            return state.withMutations(function(list) {
                  if (action.payload.isSelected === 1)
                      list.setIn([action.payload.newChoice, "selected"], false);
                  else
                      list.setIn([action.payload.newChoice, "selected"], true);

                  list.setIn([action.payload.newChoice, "order_id"], action.payload.orderId );
              });

         case LANGCOURSEFIELDS_SELECTED_SAVE_WITHIDS:
              return state.withMutations(function(list) {
                const indexOfListingToUpdate = list.findIndex(listing => {
                    return listing.get('id') === action.payload.newChoice;});
                    if (action.payload.isSelected === 1)
                        list.setIn([indexOfListingToUpdate, "selected"], false);
                    else
                        list.setIn([indexOfListingToUpdate, "selected"], true);

                    list.setIn([indexOfListingToUpdate, "order_id"], action.payload.orderId );
                });

        case LANGCOURSES_ORDER_SAVE:
            newLangCourseFields = Array<ILangCourseFieldRecord>();
            i = 0;
            action.payload.selectedCourses.forEach(langcourseField => {
                newLangCourseFields.push(recordify<ILangCourseField, ILangCourseFieldRecord>
                  ({ id: langcourseField.id, name: langcourseField.name, selected: langcourseField.selected, order_id: langcourseField.order_id }));
                i++;
            });
            return List(newLangCourseFields);


        case LANGCOURSEFIELDS_INIT:
            return LANGCOURSE_FIELDS_INITIAL_STATE;
        default: return state;
    }
};
