import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IElectiveCourseField {
    id: number;
    name: string;
    selected: boolean;
    order_id: number;
}

export interface IElectiveCourseFieldRecord extends TypedRecord<IElectiveCourseFieldRecord>, IElectiveCourseField { };
export type IElectiveCourseFieldRecords = List<IElectiveCourseFieldRecord>;
