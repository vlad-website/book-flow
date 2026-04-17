import { Injectable } from '@nestjs/common';
import * as nodemailer from 'nodemailer';

@Injectable()
export class MailService {
  private transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: 'your@gmail.com',
      pass: 'your_app_password',
    },
  });

  async sendVerificationEmail(email: string, token: string) {
    const url = `http://localhost:3000/auth/verify?token=${token}`;

    await this.transporter.sendMail({
      to: email,
      subject: 'Подтверждение email',
      html: `
        <h1>Подтвердите email</h1>
        <a href="${url}">Подтвердить</a>
      `,
    });
  }
}