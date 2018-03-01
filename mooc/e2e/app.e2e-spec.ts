import { MoocPage } from './app.po';

describe('mooc App', () => {
  let page: MoocPage;

  beforeEach(() => {
    page = new MoocPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to ag!!');
  });
});
