import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { ELECTIVECOURSEFIELDS_INIT, ELECTIVECOURSEFIELDS_RECEIVED, ELECTIVECOURSEFIELDS_SELECTED_SAVE, ELECTIVECOURSES_ORDER_SAVE, ELECTIVECOURSEFIELDS_SELECTED_SAVE_WITHIDS } from "../../constants";
import { ELECTIVECOURSE_FIELDS_INITIAL_STATE } from "./electivecoursesfields.initial-state";
import { IElectiveCourseField, IElectiveCourseFieldRecord, IElectiveCourseFieldRecords } from "./electivecoursesfields.types";

export function electivecourseFieldsReducer(state: IElectiveCourseFieldRecords = ELECTIVECOURSE_FIELDS_INITIAL_STATE, action): IElectiveCourseFieldRecords {
    switch (action.type) {
        case ELECTIVECOURSEFIELDS_RECEIVED:
            let newElectiveCourseFields = Array<IElectiveCourseFieldRecord>();
            let i = 0;
            action.payload.electivecourseFields.forEach(electivecourseField => {
                newElectiveCourseFields.push(recordify<IElectiveCourseField, IElectiveCourseFieldRecord>
                  ({ id: electivecourseField.id, name: electivecourseField.name, selected: false, order_id: 0 }));
                i++;
            });
            return List(newElectiveCourseFields);
        case ELECTIVECOURSEFIELDS_SELECTED_SAVE:
            return state.withMutations(function(list) {
                  if (action.payload.isSelected === 1)
                      list.setIn([action.payload.newChoice, "selected"], false);
                  else
                      list.setIn([action.payload.newChoice, "selected"], true);

                  list.setIn([action.payload.newChoice, "order_id"], action.payload.orderId );
              });
        case ELECTIVECOURSEFIELDS_SELECTED_SAVE_WITHIDS:
              return state.withMutations(function(list) {
                const indexOfListingToUpdate = list.findIndex(listing => {
                    return listing.get('id') === action.payload.newChoice;});
                    if (action.payload.isSelected === 1)
                        list.setIn([indexOfListingToUpdate, "selected"], false);
                    else
                        list.setIn([indexOfListingToUpdate, "selected"], true);

                    list.setIn([indexOfListingToUpdate, "order_id"], action.payload.orderId );
                });

          case ELECTIVECOURSES_ORDER_SAVE:
              newElectiveCourseFields = Array<IElectiveCourseFieldRecord>();
              i = 0;
              action.payload.selectedCourses.forEach(electivecourseField => {
                  newElectiveCourseFields.push(recordify<IElectiveCourseField, IElectiveCourseFieldRecord>
                    ({ id: electivecourseField.id, name: electivecourseField.name, selected: electivecourseField.selected, order_id: electivecourseField.order_id }));
                  i++;
              });
              return List(newElectiveCourseFields);


          case ELECTIVECOURSEFIELDS_INIT:
             return ELECTIVECOURSE_FIELDS_INITIAL_STATE;
        default: return state;
    }
};
