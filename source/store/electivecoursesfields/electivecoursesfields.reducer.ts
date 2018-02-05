import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { ELECTIVECOURSEFIELDS_INIT, ELECTIVECOURSEFIELDS_RECEIVED, ELECTIVECOURSEFIELDS_SELECTED_SAVE, ELECTIVECOURSES_ORDER_SAVE } from "../../constants";
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
                //if (action.payload.prevChoice === action.payload.newChoice)
                //  list.setIn([action.payload.prevChoice, "selected"], false);
                //else  {
                  //if (action.payload.prevChoice >= 0)
                  //    list.setIn([action.payload.prevChoice, "selected"], false);
                  //if (action.payload.newChoice >= 0)


                  if (action.payload.isSelected === 1)
                      list.setIn([action.payload.newChoice, "selected"], false);
                  else
                      list.setIn([action.payload.newChoice, "selected"], true);

                  list.setIn([action.payload.newChoice, "order_id"], action.payload.orderId );

                  //console.log("Nikos2");
                  //console.log(action.payload.orderId);

                //}
              });


              /*
              case REGIONSCHOOLS_ORDER_SAVE:
                  let newState2 = Array<IRegionRecord>();
                  newEpals = Array<IRegionSchoolRecord>();

                  i = 0, j = 0;
                  let ind2 = 0;
                  state.forEach(region => {
                      let epals: IRegionSchoolRecords;

                      epals = region.get("epals");
                      epals.forEach(epal => {
                          let newOrderId = epal.order_id;
                          for (let jjj = 0; jjj < 3; jjj++) {
                              if (typeof action.payload.selectedSchools[jjj] !== "undefined" &&
                                  epal.globalIndex === action.payload.selectedSchools[jjj].globalIndex) {
                                  newOrderId = action.payload.selectedSchools[jjj].order_id;
                                  break;
                              }
                          }
                          newEpals.push(recordify<IRRegionSchool, IRegionSchoolRecord>({ epal_id: epal.epal_id, epal_name: epal.epal_name, epal_special_case: epal.epal_special_case, globalIndex: epal.globalIndex, selected: epal.selected, order_id: newOrderId }));
                      });
                      newState2.push(recordify<IRRegion, IRegionRecord>({ region_id: region.region_id, region_name: region.region_name, epals: List(newEpals) }));
                      newEpals = Array<IRegionSchoolRecord>();
                      i++;

                  });

                  return List(newState2);
                  */

                  case ELECTIVECOURSES_ORDER_SAVE:
                      //let newState2 = Array<IRegionRecord>();
                      /*
                      let newCourses = Array<IElectiveCourseFieldRecord>();

                      i = 0;
                      //let j = 0;
                      let ind2 = 0;
                      console.log("MESSAGE1");
                      console.log(action.payload.selectedCourses);
                      state.forEach(course => {
                          console.log("MESSAGE2;")
                          let courses: IElectiveCourseFieldRecord;

                          //courses = course.get("epals");
                          //epals.forEach(epal => {
                              //let newOrderId = epal.order_id;
                              let newOrderId = course.order_id;
                              console.log(newOrderId);
                              console.log(course.get("order_id"));
                              for (let jjj = 0; jjj < 3; jjj++) {
                                  console.log("MESSAGE3;")
                                  console.log(course.name)
                                  console.log(action.payload.selectedCourses[jjj].name);
                                  console.log("MESSAGE3333;")
                                  if (typeof action.payload.selectedCourses[jjj] !== "undefined"
                                      //&&  epal.globalIndex === action.payload.selectedSchools[jjj].globalIndex) {
                                      &&  course.name === action.payload.selectedCourses[jjj].name) {
                                      console.log("MESSAGE4;")
                                      newOrderId = action.payload.selectedCourses[jjj].order_id;
                                      break;
                                  }
                              }
                              console.log("MESSAGE5;");
                              console.log(course.name);
                              newCourses.push(recordify<IElectiveCourseField, IElectiveCourseFieldRecord>
                                //({ epal_id: epal.epal_id, epal_name: epal.epal_name, epal_special_case: epal.epal_special_case, globalIndex: epal.globalIndex, selected: epal.selected, order_id: newOrderId }));
                                ({ id: course.id, name: course.name, selected: course.selected, order_id: newOrderId }));
                                console.log("MESSAGE6;")
                          });
                          console.log("MESSAGE7;")
                          //newState2.push(recordify<IRRegion, IRegionRecord>({ region_id: region.region_id, region_name: region.region_name, epals: List(newEpals) }));
                          newCourses = Array<IElectiveCourseFieldRecord>();
                          console.log("MESSAGE8;")
                          i++;

                      //});

                      return List(newCourses);
                      */




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
