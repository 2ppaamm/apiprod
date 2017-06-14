import { browser, by, element } from 'protractor';

export class MoocPage {
  navigateTo() {
    return browser.get('/');
  }

  getParagraphText() {
    return element(by.css('ag-root h1')).getText();
  }
}
