import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as bcrypt from 'bcrypt';
import { RegisterDto } from './dto/register.dto';
import { randomBytes } from 'crypto';
import { MailService } from '../mail/mail.service';

@Injectable()
export class AuthService {
  constructor(
    private prisma: PrismaService,
    private mailService: MailService,
  ) {}

  async register(dto: RegisterDto) {
    console.log(dto);
    
    const hashedPassword = await bcrypt.hash(dto.password, 10);

    const user = await this.prisma.user.create({
        data: {
        email: dto.email,
        password: hashedPassword,
        role: dto.role,
        pseudonym: dto.pseudonym,
        firstName: dto.firstName,
        lastName: dto.lastName,
        middleName: dto.middleName,
        birthDate: new Date(dto.birthDate),
        phone: dto.phone,
        },
    });

    const token = randomBytes(32).toString('hex');

    await this.prisma.verificationToken.create({
        data: {
        token,
        userId: user.id,
        expiresAt: new Date(Date.now() + 1000 * 60 * 60), // 1 час
        },
    });

    await this.mailService.sendVerificationEmail(user.email, token);

    return { message: 'Check your email' };
    }

  async verifyEmail(token: string) {
    const record = await this.prisma.verificationToken.findUnique({
      where: { token },
    });

    if (!record) {
      throw new Error('Invalid token');
    }

    if (record.expiresAt < new Date()) {
      throw new Error('Token expired');
    }

    await this.prisma.user.update({
      where: { id: record.userId },
      data: { isVerified: true },
    });

    await this.prisma.verificationToken.delete({
      where: { token },
    });

    return { message: 'Email verified' };
  }
}