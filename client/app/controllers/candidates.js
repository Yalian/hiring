import Controller from '@ember/controller';
import { action } from '@ember/object';

export default class CandidatesController extends Controller {
  @action
  addNew() {
    if (this.name && this.age) {
      const candidate = this.store.createRecord('Applicant', {
        name: this.name,
        age: this.age,
      });
      candidate.save().catch((err) => {
        let parsedErrorsToShow = '';

        err.errors.forEach((e) => (parsedErrorsToShow += `${e.detail} \n`));

        alert(parsedErrorsToShow);
      });

      this.set('name', '');
      this.set('age', '');
    }
  }
}
