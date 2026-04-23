import { Injectable } from '@nestjs/common';
import * as nodemailer from 'nodemailer';

@Injectable()
export class MailService {
  private transporter = nodemailer.createTransport({
    host: "sandbox.smtp.mailtrap.io",
    port: 2525,
    auth: {
      user: "0ea4d9eca4f1f3",
      pass: "2a1e588f41d061"
    }
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