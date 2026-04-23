import React, { useEffect, useMemo, useState } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Search, MessageCircle, CreditCard, ShieldCheck, Languages, Users, Star, Lock, CheckCircle2, LogOut, Globe2, Sparkles } from 'lucide-react';

const COOKIE_KEYS = {
  CURRENT_USER: 'swahili_connect_current_user',
  USERS: 'swahili_connect_users',
  PAID: 'swahili_connect_paid',
  PHONE: 'swahili_connect_phone',
};

function setCookie(name, value, days = 7) {
  const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

function getCookie(name) {
  const match = document.cookie.split('; ').find((row) => row.startsWith(`${name}=`));
  return match ? decodeURIComponent(match.split('=')[1]) : '';
}

function deleteCookie(name) {
  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax`;
}

function readUsersFromCookies() {
  try {
    const raw = getCookie(COOKIE_KEYS.USERS);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveUsersToCookies(users) {
  setCookie(COOKIE_KEYS.USERS, JSON.stringify(users), 30);
}

const learners = [
  {
    id: 1,
    name: 'Sophie',
    country: 'France',
    level: 'Beginner',
    goal: 'Wants to build confidence in everyday Swahili conversation before visiting East Africa.',
    rate: 'KES 650',
    badge: 'Popular',
  },
  {
    id: 2,
    name: 'Luca',
    country: 'Italy',
    level: 'Intermediate',
    goal: 'Needs help with natural speaking and travel expressions for longer stays.',
    rate: 'KES 780',
    badge: 'Top Rated',
  },
  {
    id: 3,
    name: 'Emma',
    country: 'United Kingdom',
    level: 'Beginner',
    goal: 'Looking for relaxed practice sessions focused on greetings and daily speech.',
    rate: 'KES 700',
    badge: 'New',
  },
  {
    id: 4,
    name: 'Noah',
    country: 'Germany',
    level: 'Advanced Beginner',
    goal: 'Wants more fluency, better pronunciation, and natural phrasing.',
    rate: 'KES 820',
    badge: 'Fast Learner',
  },
  {
    id: 5,
    name: 'Mia',
    country: 'USA',
    level: 'Beginner',
    goal: 'Learning Swahili for culture, travel, and meaningful conversation practice.',
    rate: 'KES 900',
    badge: 'Premium',
  },
  {
    id: 6,
    name: 'Oliver',
    country: 'Netherlands',
    level: 'Intermediate',
    goal: 'Needs structured chat sessions to improve confidence in real conversation.',
    rate: 'KES 760',
    badge: 'Verified',
  },
  {
    id: 7,
    name: 'Isabella',
    country: 'Spain',
    level: 'Beginner',
    goal: 'Wants to master common phrases and sound more natural when speaking.',
    rate: 'KES 680',
    badge: 'Active',
  },
  {
    id: 8,
    name: 'Ethan',
    country: 'USA',
    level: 'Intermediate',
    goal: 'Looking for frequent conversation practice with feedback on sentence flow.',
    rate: 'KES 840',
    badge: 'Serious Learner',
  },
];

const learnerReplies = [
  'That makes sense. Can we try another example?',
  'Asante! I am starting to understand this better.',
  'How would I say that in a more natural way?',
  'Can we practice a short real-life conversation next?',
  'I like that explanation. Please teach me one more phrase.',
  'That is helpful. How do I respond politely in that situation?',
  'Great. Can you help me improve my pronunciation too?',
  'Nice. I want to sound more fluent when I speak.',
];

const openingMessages = [
  [
    { from: 'learner', text: 'Habari! I want to improve my greetings in Swahili.' },
    { from: 'you', text: 'Karibu! We can start with simple greetings and natural replies.' },
    { from: 'learner', text: 'Perfect. I want to sound more confident when I speak.' },
  ],
  [
    { from: 'learner', text: 'Hi! Can you teach me how to introduce myself politely?' },
    { from: 'you', text: 'Yes. We can begin with “Jina langu ni…” and practice a full introduction.' },
    { from: 'learner', text: 'That would be really helpful for me.' },
  ],
  [
    { from: 'learner', text: 'Hello! I want to learn useful travel phrases in Swahili.' },
    { from: 'you', text: 'Absolutely. Let’s focus on phrases you can use at the airport, hotel, and market.' },
    { from: 'learner', text: 'Amazing. I want practical phrases first.' },
  ],
  [
    { from: 'learner', text: 'Habari yako? I am practicing short conversations today.' },
    { from: 'you', text: 'Nzuri sana. Let’s build a simple back-and-forth conversation together.' },
    { from: 'learner', text: 'Yes please. I want it to feel natural.' },
  ],
];

function getInitialMessages(personId) {
  return openingMessages[personId % openingMessages.length];
}

function Brand() {
  return (
    <div className="flex items-center gap-3">
      <div className="w-11 h-11 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-bold shadow-sm">SC</div>
      <div>
        <h1 className="font-bold text-lg tracking-tight">Swahili Connect</h1>
        <p className="text-xs text-slate-500">Learn through real conversation</p>
      </div>
    </div>
  );
}

function AuthCard({ mode, onSubmit }) {
  const [form, setForm] = useState({ name: '', email: '', password: '' });

  return (
    <Card className="rounded-[28px] border-white/60 bg-white/90 shadow-xl backdrop-blur">
      <CardHeader>
        <CardTitle className="text-2xl">{mode === 'signup' ? 'Create account' : 'Sign in to continue'}</CardTitle>
        <CardDescription>
          {mode === 'signup'
            ? 'Create your account to access the Swahili learner marketplace.'
            : 'Welcome back. Sign in to continue to your homepage.'}
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {mode === 'signup' && (
          <Input
            className="rounded-2xl h-12"
            placeholder="Full name"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
          />
        )}
        <Input
          className="rounded-2xl h-12"
          placeholder="Email address"
          type="email"
          value={form.email}
          onChange={(e) => setForm({ ...form, email: e.target.value })}
        />
        <Input
          className="rounded-2xl h-12"
          placeholder="Password"
          type="password"
          value={form.password}
          onChange={(e) => setForm({ ...form, password: e.target.value })}
        />
        <Button className="w-full rounded-2xl h-12" onClick={() => onSubmit(form)}>
          {mode === 'signup' ? 'Create Account' : 'Sign In'}
        </Button>
      </CardContent>
    </Card>
  );
}

function PaymentModal({ openPerson, paid, setPaid, phone, setPhone }) {
  const [loading, setLoading] = useState(false);
  const [status, setStatus] = useState('idle');
  const [paymentMessage, setPaymentMessage] = useState('');

  const initiatePayment = async () => {
    setLoading(true);
    setStatus('processing');
    setPaymentMessage('');

    try {
      const response = await fetch('/api/megapay/initiate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          amount: 100,
          msisdn: phone,
          reference: `swahili-chat-${openPerson?.id || 'learner'}-${Date.now()}`,
        }),
      });

      const result = await response.json();

      if (!response.ok || result.success === false) {
        throw new Error(result.message || result.providerResponse?.message || 'MegaPay payment failed.');
      }

      setPaid(true);
      setCookie(COOKIE_KEYS.PAID, 'true', 7);
      setCookie(COOKIE_KEYS.PHONE, phone, 7);
      setStatus('success');
      setPaymentMessage('STK Push sent successfully. Confirm the payment on your phone to unlock chat.');
    } catch (error) {
      setStatus('error');
      setPaymentMessage(error instanceof Error ? error.message : 'Payment request failed.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Dialog>
      <DialogTrigger asChild>
        <Button className="w-full rounded-2xl">{paid ? 'Open Chat' : 'Pay KES 100 to Chat'}</Button>
      </DialogTrigger>
      <DialogContent className="rounded-[28px]">
        <DialogHeader>
          <DialogTitle>Unlock chat access</DialogTitle>
          <DialogDescription>
            Pay a one-time KES 100 access fee before chatting with {openPerson?.name || 'this learner'}.
          </DialogDescription>
        </DialogHeader>

        <div className="space-y-4">
          <div className="rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
            Enter your phone number to receive the MegaPay STK Push request and unlock chat access.
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div className="rounded-2xl border p-4">
              <p className="text-sm text-slate-500">Access fee</p>
              <p className="text-2xl font-semibold">KES 100</p>
            </div>
            <div className="rounded-2xl border p-4">
              <p className="text-sm text-slate-500">Selected learner</p>
              <p className="text-lg font-semibold">{openPerson?.name || '—'}</p>
            </div>
          </div>

          <Input
            className="rounded-2xl h-12"
            placeholder="Phone number e.g. 2547XXXXXXXX"
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
          />

          <Button className="w-full rounded-2xl h-12" onClick={initiatePayment} disabled={loading || paid || !phone}>
            {loading ? 'Processing STK Push...' : paid ? 'Payment Confirmed' : 'Pay via MegaPay'}
          </Button>

          {status === 'success' && (
            <div className="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 text-sm flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4" /> {paymentMessage || 'Payment request accepted. Chat is now unlocked.'}
            </div>
          )}

          {status === 'error' && (
            <div className="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700 text-sm">
              {paymentMessage || 'Could not initiate MegaPay payment.'}
            </div>
          )}
        </div>
      </DialogContent>
    </Dialog>
  );
}

function ChatArea({ person, paid }) {
  const [messages, setMessages] = useState(getInitialMessages(person.id));
  const [text, setText] = useState('');

  useEffect(() => {
    setMessages(getInitialMessages(person.id));
    setText('');
  }, [person.id]);

  const sendMessage = () => {
    if (!text.trim() || !paid) return;
    const randomReply = learnerReplies[Math.floor(Math.random() * learnerReplies.length)];
    setMessages((prev) => [...prev, { from: 'you', text }, { from: 'learner', text: randomReply }]);
    setText('');
  };

  return (
    <Card className="rounded-[28px] border-white/60 bg-white/90 shadow-lg backdrop-blur h-full">
      <CardHeader className="border-b border-slate-100">
        <CardTitle className="flex items-center gap-3 text-lg">
          <Avatar className="w-11 h-11">
            <AvatarFallback>{person.name.slice(0, 2).toUpperCase()}</AvatarFallback>
          </Avatar>
          <div>
            <div>{person.name}</div>
            <CardDescription>{person.country} • {person.level}</CardDescription>
          </div>
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-4 p-5">
        <div className="h-[420px] overflow-y-auto rounded-2xl bg-slate-50 p-4 space-y-3">
          {messages.map((message, index) => (
            <div
              key={index}
              className={`max-w-[85%] rounded-2xl px-4 py-3 text-sm ${message.from === 'you' ? 'ml-auto bg-slate-950 text-white' : 'bg-white border border-slate-200'}`}
            >
              {message.text}
            </div>
          ))}
          {!paid && (
            <div className="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700 flex items-center gap-2">
              <Lock className="w-4 h-4" /> Pay KES 100 first to start sending messages.
            </div>
          )}
        </div>

        <Textarea
          className="rounded-2xl min-h-[96px]"
          placeholder={paid ? 'Type your message in English or Swahili...' : 'Chat locked until payment is verified'}
          value={text}
          disabled={!paid}
          onChange={(e) => setText(e.target.value)}
        />
        <Button className="rounded-2xl h-11" onClick={sendMessage} disabled={!paid || !text.trim()}>
          Send Message
        </Button>
      </CardContent>
    </Card>
  );
}

function LearnerCard({ person, onSelect, selectedId, paid, setPaid, phone, setPhone }) {
  return (
    <motion.div whileHover={{ y: -4 }} transition={{ duration: 0.2 }}>
      <Card className={`rounded-[28px] border-white/60 bg-white/90 shadow-lg backdrop-blur h-full ${selectedId === person.id ? 'ring-2 ring-slate-950' : ''}`}>
        <CardHeader>
          <div className="flex items-start justify-between gap-3">
            <div className="flex items-center gap-3">
              <Avatar className="w-12 h-12">
                <AvatarFallback>{person.name.slice(0, 2).toUpperCase()}</AvatarFallback>
              </Avatar>
              <div>
                <CardTitle className="text-lg">{person.name}</CardTitle>
                <CardDescription>{person.country} • {person.level}</CardDescription>
              </div>
            </div>
            <Badge variant="secondary" className="rounded-full">{person.badge}</Badge>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <p className="text-sm text-slate-600">{person.goal}</p>
          <div className="flex items-center justify-between rounded-2xl bg-slate-50 p-4">
            <div>
              <p className="text-xs text-slate-500">Displayed amount</p>
              <p className="text-xl font-semibold">{person.rate}</p>
            </div>
            <Star className="w-5 h-5" />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <Button variant="outline" className="rounded-2xl" onClick={() => onSelect(person)}>
              View Chat
            </Button>
            <PaymentModal openPerson={person} paid={paid} setPaid={setPaid} phone={phone} setPhone={setPhone} />
          </div>
        </CardContent>
      </Card>
    </motion.div>
  );
}

function LoginScreen({ handleAuth }) {
  return (
    <div className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,23,42,0.08),_transparent_30%),linear-gradient(135deg,#f8fafc_0%,#eef2ff_50%,#f8fafc_100%)] text-slate-900">
      <div className="max-w-7xl mx-auto min-h-screen px-4 md:px-6 py-8 grid lg:grid-cols-[1.05fr_0.95fr] gap-10 items-center">
        <div className="space-y-8">
          <Brand />
          <div className="space-y-5 max-w-2xl">
            <Badge className="rounded-full px-4 py-1 bg-slate-950 text-white hover:bg-slate-950">Premium Swahili conversation platform</Badge>
            <h1 className="text-5xl md:text-7xl font-bold tracking-tight leading-[0.95]">
              Meet learners, start conversations, and earn through Swahili practice.
            </h1>
            <p className="text-lg text-slate-600 max-w-xl">
              Access a clean, premium chat platform where every conversation begins with secure sign in and a simple payment unlock via MegaPay.
            </p>
          </div>

          <div className="grid sm:grid-cols-3 gap-4">
            <div className="rounded-[24px] bg-white/80 backdrop-blur border border-white/70 p-5 shadow-sm">
              <Users className="w-5 h-5 mb-3" />
              <p className="font-semibold">Curated profiles</p>
              <p className="text-sm text-slate-500 mt-1">Browse learners by level, country, and displayed amount.</p>
            </div>
            <div className="rounded-[24px] bg-white/80 backdrop-blur border border-white/70 p-5 shadow-sm">
              <CreditCard className="w-5 h-5 mb-3" />
              <p className="font-semibold">KES 100 access</p>
              <p className="text-sm text-slate-500 mt-1">Chat access unlocks after a simple MegaPay payment request.</p>
            </div>
            <div className="rounded-[24px] bg-white/80 backdrop-blur border border-white/70 p-5 shadow-sm">
              <Languages className="w-5 h-5 mb-3" />
              <p className="font-semibold">Swahili first</p>
              <p className="text-sm text-slate-500 mt-1">Designed for simple, natural language practice and connection.</p>
            </div>
          </div>
        </div>

        <div className="w-full max-w-xl mx-auto lg:mx-0 lg:justify-self-end">
          <Tabs defaultValue="signin" className="space-y-4">
            <TabsList className="grid grid-cols-2 rounded-2xl bg-white/80 backdrop-blur">
              <TabsTrigger value="signin">Sign In</TabsTrigger>
              <TabsTrigger value="signup">Sign Up</TabsTrigger>
            </TabsList>
            <TabsContent value="signin">
              <AuthCard mode="signin" onSubmit={(form) => handleAuth(form, 'signin')} />
            </TabsContent>
            <TabsContent value="signup">
              <AuthCard mode="signup" onSubmit={(form) => handleAuth(form, 'signup')} />
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
}

export default function SwahiliChatPlatformStarter() {
  const [authenticated, setAuthenticated] = useState(false);
  const [userName, setUserName] = useState('Teacher');
  const [search, setSearch] = useState('');
  const [selectedPerson, setSelectedPerson] = useState(learners[0]);
  const [paid, setPaid] = useState(false);
  const [phone, setPhone] = useState('');

  useEffect(() => {
    const currentUser = getCookie(COOKIE_KEYS.CURRENT_USER);
    const savedPaid = getCookie(COOKIE_KEYS.PAID);
    const savedPhone = getCookie(COOKIE_KEYS.PHONE);

    if (currentUser) {
      try {
        const parsed = JSON.parse(currentUser);
        setAuthenticated(true);
        setUserName(parsed.name || 'Teacher');
      } catch {
        setAuthenticated(false);
      }
    }

    if (savedPaid === 'true') setPaid(true);
    if (savedPhone) setPhone(savedPhone);
  }, []);

  const filteredPeople = useMemo(() => {
    const q = search.toLowerCase();
    return learners.filter(
      (person) =>
        person.name.toLowerCase().includes(q) ||
        person.country.toLowerCase().includes(q) ||
        person.level.toLowerCase().includes(q)
    );
  }, [search]);

  const handleAuth = (form, mode) => {
    const users = readUsersFromCookies();

    if (mode === 'signup') {
      const existingUser = users.find((user) => user.email === form.email);
      if (existingUser) {
        alert('That email is already registered. Please sign in.');
        return;
      }

      const newUser = {
        id: Date.now(),
        name: form.name || 'Teacher',
        email: form.email,
        password: form.password,
      };
      const updatedUsers = [...users, newUser];
      saveUsersToCookies(updatedUsers);
      setCookie(COOKIE_KEYS.CURRENT_USER, JSON.stringify(newUser), 7);
      setAuthenticated(true);
      setUserName(newUser.name);
      return;
    }

    const matchedUser = users.find(
      (user) => user.email === form.email && user.password === form.password
    );

    if (matchedUser) {
      setCookie(COOKIE_KEYS.CURRENT_USER, JSON.stringify(matchedUser), 7);
      setAuthenticated(true);
      setUserName(matchedUser.name || 'Teacher');
      return;
    }

    alert('Invalid email or password. Please sign up first or use correct login details.');
  };

  const logout = () => {
    deleteCookie(COOKIE_KEYS.CURRENT_USER);
    deleteCookie(COOKIE_KEYS.PAID);
    deleteCookie(COOKIE_KEYS.PHONE);
    setAuthenticated(false);
    setUserName('Teacher');
    setPaid(false);
    setPhone('');
    setSearch('');
    setSelectedPerson(learners[0]);
  };

  if (!authenticated) {
    return <LoginScreen handleAuth={handleAuth} />;
  }

  return (
    <div className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,23,42,0.06),_transparent_28%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] text-slate-900">
      <header className="sticky top-0 z-30 border-b border-slate-200/70 bg-white/85 backdrop-blur-xl">
        <div className="max-w-7xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between gap-4">
          <Brand />
          <div className="hidden md:flex items-center gap-2">
            <Badge variant="secondary" className="rounded-full px-3 py-1">Welcome, {userName}</Badge>
            <Button variant="outline" className="rounded-2xl" onClick={logout}>
              <LogOut className="w-4 h-4 mr-2" /> Logout
            </Button>
          </div>
          <div className="md:hidden">
            <Button variant="outline" size="icon" className="rounded-2xl" onClick={logout}>
              <LogOut className="w-4 h-4" />
            </Button>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 md:px-6 py-8 space-y-8">
        <section className="grid xl:grid-cols-[1.05fr_0.95fr] gap-6 items-stretch">
          <Card className="rounded-[32px] bg-slate-950 text-white border-slate-950 shadow-xl overflow-hidden">
            <CardContent className="p-8 md:p-10 h-full flex flex-col justify-between">
              <div className="space-y-6">
                <Badge className="w-fit rounded-full bg-white/10 text-white hover:bg-white/10">Swahili learning marketplace</Badge>
                <div className="space-y-4 max-w-2xl">
                  <h1 className="text-4xl md:text-6xl font-bold tracking-tight leading-[1]">
                    Connect with learners and grow through meaningful Swahili conversations.
                  </h1>
                  <p className="text-slate-300 text-base md:text-lg max-w-xl">
                    Browse profiles from Europe and the USA, view displayed amounts, and unlock conversations instantly after a KES 100 MegaPay payment.
                  </p>
                </div>
              </div>

              <div className="grid sm:grid-cols-3 gap-4 mt-8">
                <div className="rounded-[24px] bg-white/5 p-5 border border-white/10">
                  <Users className="w-5 h-5 mb-3" />
                  <p className="font-semibold">Global learners</p>
                  <p className="text-sm text-slate-300 mt-1">Explore profiles across Europe and the USA.</p>
                </div>
                <div className="rounded-[24px] bg-white/5 p-5 border border-white/10">
                  <CreditCard className="w-5 h-5 mb-3" />
                  <p className="font-semibold">Secure unlock</p>
                  <p className="text-sm text-slate-300 mt-1">KES 100 access powered by MegaPay.</p>
                </div>
                <div className="rounded-[24px] bg-white/5 p-5 border border-white/10">
                  <Globe2 className="w-5 h-5 mb-3" />
                  <p className="font-semibold">Responsive design</p>
                  <p className="text-sm text-slate-300 mt-1">A clean user experience for desktop and mobile.</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="rounded-[32px] border-white/60 bg-white/90 shadow-xl backdrop-blur">
            <CardContent className="p-8 md:p-10 h-full flex flex-col justify-between">
              <div className="space-y-6">
                <div className="space-y-2">
                  <div className="flex items-center gap-2 text-slate-500 text-sm"><Sparkles className="w-4 h-4" /> Platform summary</div>
                  <h2 className="text-3xl font-bold tracking-tight">A real user homepage</h2>
                  <p className="text-slate-600">
                    This page now focuses only on the user experience: sign in first, browse learners, unlock chat access, and start practicing Swahili.
                  </p>
                </div>

                <div className="space-y-3">
                  <div className="rounded-2xl bg-slate-50 p-4 flex items-start gap-3">
                    <ShieldCheck className="w-5 h-5 mt-0.5" />
                    <div>
                      <p className="font-medium">Authentication first</p>
                      <p className="text-sm text-slate-500">Every user must sign in or create an account before reaching the homepage.</p>
                    </div>
                  </div>
                  <div className="rounded-2xl bg-slate-50 p-4 flex items-start gap-3">
                    <MessageCircle className="w-5 h-5 mt-0.5" />
                    <div>
                      <p className="font-medium">More natural conversations</p>
                      <p className="text-sm text-slate-500">Chat replies now vary randomly to make each conversation feel less repetitive.</p>
                    </div>
                  </div>
                  <div className="rounded-2xl bg-slate-50 p-4 flex items-start gap-3">
                    <Languages className="w-5 h-5 mt-0.5" />
                    <div>
                      <p className="font-medium">Focused learner market</p>
                      <p className="text-sm text-slate-500">Profiles shown here are limited to Europe and the USA.</p>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </section>

        <section className="space-y-5">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h2 className="text-3xl font-bold tracking-tight">Learners looking to practice Swahili</h2>
              <p className="text-slate-600 mt-2">Browse profiles, select a chat, and view each person’s displayed amount.</p>
            </div>
            <div className="relative w-full lg:w-96">
              <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
              <Input
                className="rounded-2xl pl-10 h-12 bg-white"
                placeholder="Search by name, country, or level"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>

          <div className="grid xl:grid-cols-[1.1fr_0.9fr] gap-6 items-start">
            <div className="grid md:grid-cols-2 gap-4">
              {filteredPeople.map((person) => (
                <LearnerCard
                  key={person.id}
                  person={person}
                  onSelect={(p) => setSelectedPerson(p)}
                  selectedId={selectedPerson?.id}
                  paid={paid}
                  setPaid={setPaid}
                  phone={phone}
                  setPhone={setPhone}
                />
              ))}
            </div>
            <div className="xl:sticky xl:top-24">
              <ChatArea person={selectedPerson} paid={paid} />
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
